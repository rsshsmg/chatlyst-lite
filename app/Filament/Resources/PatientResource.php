<?php

namespace App\Filament\Resources;

use App\Enums\AddressType;
use App\Enums\BloodType;
use App\Enums\ContactType;
use App\Enums\Gender;
use App\Enums\GuarantorType;
use App\Enums\IdentityType;
use App\Enums\MaritalStatus;
use App\Enums\RelationType;
use App\Enums\ReligionType;
use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers;
use App\Filament\Resources\PatientResource\RelationManagers\IdentitiesRelationManager;
use App\Models\Contact;
use App\Models\Education;
use App\Models\Guarantor;
use App\Models\Patient;
use App\Models\PatientIdentity;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'icon-hospital-user';

    public static function getNavigationGroup(): ?string
    {
        return 'Customers';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Profile')
                            ->icon('heroicon-s-user')
                            ->schema([
                                Split::make([
                                    Grid::make()
                                        ->schema([
                                            Section::make()
                                                ->columns(2)
                                                ->schema([
                                                    Forms\Components\TextInput::make('full_name')
                                                        ->required()
                                                        ->maxLength(255),
                                                    Forms\Components\TextInput::make('nickname')
                                                        ->maxLength(255),
                                                    Forms\Components\TextInput::make('email')
                                                        ->email()
                                                        ->maxLength(255),
                                                    Forms\Components\Select::make('gender')
                                                        ->required()
                                                        ->placeholder('Select Gender')
                                                        ->options(Gender::array()),
                                                    Forms\Components\DatePicker::make('date_of_birth')
                                                        ->required()
                                                        ->reactive()
                                                        ->afterStateUpdated(fn(?string $state, Set $set) => self::calculateAge($state, $set)),
                                                    Forms\Components\TextInput::make('age')
                                                        ->disabled()
                                                        ->afterStateHydrated(function (Get $get, Set $set) {
                                                            $age = self::calculateAge($get('date_of_birth'));
                                                            $set('age', $age);
                                                        }),
                                                    Forms\Components\TextInput::make('place_of_birth')
                                                        ->maxLength(255),
                                                    Forms\Components\Select::make('blood_type')
                                                        ->options(BloodType::array()),
                                                    Forms\Components\Select::make('religion')
                                                        ->options(ReligionType::array()),
                                                    Forms\Components\Select::make('marital_status')
                                                        ->options(MaritalStatus::array()),

                                                    Forms\Components\Fieldset::make('Phones')
                                                        ->columns(1)
                                                        ->schema([
                                                            Forms\Components\Repeater::make('phones')
                                                                ->simple(
                                                                    PhoneInput::make('value')->label('Nomor Telepon'),
                                                                )
                                                                ->defaultItems(1),
                                                        ]),
                                                    Forms\Components\Fieldset::make('Emails')
                                                        ->columns(1)
                                                        ->schema([
                                                            Forms\Components\Repeater::make('emails')
                                                                ->simple(
                                                                    TextInput::make('value')->label('Email Address'),
                                                                )
                                                                ->defaultItems(1),
                                                        ])
                                                ]),
                                            Section::make('Pendidikan')
                                                ->columns(2)
                                                ->schema([
                                                    Forms\Components\Select::make('education')
                                                        ->label('Education Level')
                                                        ->required()
                                                        ->options(Education::pluck('name', 'id')),
                                                    Forms\Components\TextInput::make('institution')
                                                        ->label('Education Institution'),
                                                ]),

                                            Grid::make()
                                                ->columns(1)
                                                ->schema([
                                                    // Forms\Components\Actions::make([
                                                    //     Forms\Components\Actions\Action::make('addPhone')
                                                    //         ->label('Tambah Nomor Telepon')
                                                    //         ->action(function ($get, $set) {
                                                    //             $contacts = $get('contacts') ?? [];
                                                    //             $contacts[] = [
                                                    //                 'contact_type' => ContactType::Phone->value,
                                                    //                 'value' => '',
                                                    //             ];
                                                    //             $set('contacts', $contacts);
                                                    //         }),
                                                    //     Forms\Components\Actions\Action::make('addEmail')
                                                    //         ->label('Tambah Email')
                                                    //         ->action(function ($get, $set) {
                                                    //             $contacts = $get('contacts') ?? [];
                                                    //             $contacts[] = [
                                                    //                 'contact_type' => ContactType::Email->value,
                                                    //                 'value' => '',
                                                    //             ];
                                                    //             $set('contacts', $contacts);
                                                    //         }),
                                                    //     Forms\Components\Actions\Action::make('addWhatsapp')
                                                    //         ->label('Tambah WhatsApp')
                                                    //         ->action(function ($get, $set) {
                                                    //             $contacts = $get('contacts') ?? [];
                                                    //             $contacts[] = [
                                                    //                 'contact_type' => ContactType::Whatsapp->value,
                                                    //                 'value' => '',
                                                    //             ];
                                                    //             $set('contacts', $contacts);
                                                    //         }),
                                                    // ]),

                                                    // Repeater di bawahnya
                                                    Forms\Components\Repeater::make('patient_guardians')
                                                        ->schema([
                                                            Forms\Components\Toggle::make('is_primary')
                                                                ->label('Kontak Utama')
                                                                ->default(false),

                                                            Forms\Components\Select::make('relation_type')
                                                                ->label('Hubungan')
                                                                ->default(RelationType::SELF->value)
                                                                ->options(RelationType::array())
                                                                ->required(),

                                                            Forms\Components\Fieldset::make('Person')
                                                                ->label('Data Contact')
                                                                ->columns(6)
                                                                ->schema([
                                                                    Forms\Components\TextInput::make('person.full_name')
                                                                        ->label('Nama Lengkap')
                                                                        ->required()
                                                                        ->maxLength(255)
                                                                        ->columnSpan(3),
                                                                    Forms\Components\TextInput::make('person.nickname')
                                                                        ->label('Nama Panggilan')
                                                                        ->maxLength(255)
                                                                        ->columnSpan(3),
                                                                    Forms\Components\TextInput::make('person.place_of_birth')
                                                                        ->label('Tempat Lahir')
                                                                        ->maxLength(255)
                                                                        ->columnSpan(2),
                                                                    Forms\Components\DatePicker::make('person.date_of_birth')
                                                                        ->label('Tanggal Lahir')
                                                                        ->required()
                                                                        ->reactive()
                                                                        ->afterStateUpdated(function (String $state, Set $set) {
                                                                            $age = self::calculateAge($state);
                                                                            $set('person.age', $age);
                                                                        })
                                                                        ->columnSpan(2),
                                                                    Forms\Components\TextInput::make('person.age')
                                                                        ->label('Usia')
                                                                        ->disabled()
                                                                        ->columnSpan(2),
                                                                ]),
                                                            Forms\Components\Fieldset::make('Phones')
                                                                ->columns(1)
                                                                ->schema([
                                                                    Forms\Components\Repeater::make('phones')
                                                                        ->simple(
                                                                            PhoneInput::make('value')->label('Nomor Telepon'),
                                                                        )
                                                                        ->defaultItems(1),
                                                                ]),
                                                            Forms\Components\Fieldset::make('Emails')
                                                                ->columns(1)
                                                                ->schema([
                                                                    Forms\Components\Repeater::make('emails')
                                                                        ->simple(
                                                                            TextInput::make('value')->label('Email Address'),
                                                                        )
                                                                        ->defaultItems(1),
                                                                ])
                                                        ])
                                                        ->label('Kontak')
                                                        ->addActionLabel('Tambah Kontak')
                                                        ->reorderable(false),

                                                ]),
                                        ]),

                                    Section::make('Reference Patient ID')
                                        ->schema([

                                            Forms\Components\TextInput::make('ref_patient_code')
                                                ->label('Patient ID')
                                                ->maxLength(config('patient.patientid_max_length'))
                                                ->readOnly()
                                                // ->hint('Need help?')
                                                ->hintIcon('heroicon-m-question-mark-circle', 'If the patient forgets their Patient ID (NORM), synchronize with SIMRS to retrieve the data.')
                                                ->suffixAction(
                                                    Forms\Components\Actions\Action::make('syncSimrs')
                                                        ->label('Sync')
                                                        ->icon('heroicon-m-arrow-path')
                                                        // ->color('secondary')
                                                        ->form(function () {
                                                            // Ambil hasil dari SIMRS (dummy)
                                                            $matches = [
                                                                ['norm' => 'SH-123456', 'score' => 90],
                                                                ['norm' => 'SH-250011', 'score' => 45],
                                                            ];

                                                            return [
                                                                Forms\Components\Radio::make('selected_norm')
                                                                    ->label('Beberapa NORM serupa ditemukan. Pilih yang paling sesuai dengan pasien.')
                                                                    ->options(
                                                                        collect($matches)
                                                                            ->mapWithKeys(fn($match) => [
                                                                                $match['norm'] => $match['norm'] . ' (' . $match['score'] . '%)',
                                                                            ])
                                                                            ->toArray()
                                                                    )
                                                                    ->required()
                                                            ];
                                                        })
                                                        ->action(function (array $data, Set $set) {
                                                            // Set ke field input
                                                            $set('ref_patient_code', $data['selected_norm']);
                                                        })
                                                        ->modalHeading('Beberapa data pasien ditemukan di SIMRS')
                                                        ->modalSubmitActionLabel('Gunakan NORM Ini')
                                                ),

                                        ])
                                        ->grow(false),
                                ])
                                    ->from('md')
                            ]),
                        Tabs\Tab::make('Identities')
                            ->icon('heroicon-s-identification')
                            ->badge(fn(?Model $record): int => $record?->person->identities()->count() ?? 0)
                            ->badgeColor(fn(?Model $record) => ($record?->person->identities()->count() ?? 0) > 0 ? 'success' : 'danger')
                            ->schema([
                                Forms\Components\Repeater::make('identities')
                                    // ->relationship('')
                                    ->addActionLabel('Add legal identity')
                                    ->grid(2)
                                    ->itemLabel(fn(array $state): ?string => IdentityType::from($state['identity_type'])->label() ?? null)
                                    ->schema([
                                        Forms\Components\Select::make('identity_type')
                                            ->required()
                                            ->options(IdentityType::array())
                                            ->default(IdentityType::KTP->value)
                                            ->selectablePlaceholder(false),
                                        Forms\Components\TextInput::make('number')
                                            ->required()
                                            ->maxLength(16),
                                        Forms\Components\DatePicker::make('issued_at'),
                                    ])
                            ]),
                        Tabs\Tab::make('Addresses')
                            ->icon('heroicon-s-map-pin')
                            ->badge(fn(?Model $record): int => $record?->person->addresses()->count() ?? 0)
                            ->badgeColor(fn(?Model $record) => ($record?->person->addresses()->count() ?? 0) > 0 ? 'success' : 'danger')
                            ->schema([
                                Forms\Components\Repeater::make('addresses')
                                    // ->relationship()
                                    ->addActionLabel('Add new address')
                                    ->grid(2)
                                    ->itemLabel(fn(array $state): ?string => AddressType::from($state['address_type'])->label() ?? null)
                                    ->schema([
                                        Forms\Components\Select::make('address_type')
                                            ->required()
                                            ->options(AddressType::array())
                                            ->default(AddressType::RESIDENTIAL->value)
                                            ->selectablePlaceholder(false),
                                        Forms\Components\TextInput::make('address')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('city')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('province')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('postal_code')
                                            ->maxLength(10),
                                        Forms\Components\TextInput::make('country')
                                            ->maxLength(255),
                                        Forms\Components\Toggle::make('is_primary')
                                    ])
                            ]),
                        // Tabs\Tab::make('Contacts')
                        //     ->icon('heroicon-s-phone')
                        //     ->badge(fn(?Model $record): int => $record?->contacts()->count() ?? 0)
                        //     ->badgeColor(fn(?Model $record) => ($record?->contacts()->count() ?? 0) > 0 ? 'success' : 'danger')
                        //     ->schema([
                        //         Forms\Components\Repeater::make('contacts')
                        //             ->schema([
                        //                 Forms\Components\Select::make('relation_type')
                        //                     ->label('Hubungan')
                        //                     ->default(RelationType::SELF->value)
                        //                     ->options(RelationType::array()),

                        //                 Forms\Components\Toggle::make('is_primary')
                        //                     ->default(false),

                        //                 Forms\Components\Fieldset::make('Kontak')
                        //                     ->columns(1)
                        //                     ->schema([
                        //                         Forms\Components\Repeater::make('phones')
                        //                             ->simple(
                        //                                 Forms\Components\TextInput::make('value')->label('Nomor Telepon'),
                        //                             )
                        //                             ->defaultItems(1),

                        //                         Forms\Components\Repeater::make('emails')
                        //                             ->simple(
                        //                                 Forms\Components\TextInput::make('value')->label('Alamat Email'),
                        //                             )
                        //                             ->defaultItems(1),

                        //                         Forms\Components\Repeater::make('whatsapps')
                        //                             ->simple(
                        //                                 Forms\Components\TextInput::make('value')->label('Nomor WhatsApp'),
                        //                             )
                        //                             ->defaultItems(1),
                        //                     ])
                        //             ])
                        //             ->label('Kontak Pasien')
                        //             ->addActionLabel('Tambah Kontak Pasien')
                        //             ->reorderable(false),
                        //     ]),
                        Tabs\Tab::make('Guarantors')
                            ->icon('heroicon-s-building-library')
                            ->badge(fn(?Model $record): int => $record?->guarantors()->count() ?? 0)
                            ->badgeColor(fn(?Model $record) => ($record?->guarantors()->count() ?? 0) > 0 ? 'success' : 'danger')
                            ->schema([
                                Forms\Components\Repeater::make('guarantors')
                                    ->relationship()
                                    ->addActionLabel('Add guarantor')
                                    ->grid(2)
                                    ->itemLabel(fn(array $state): ?string => ($state && array_key_exists('guarantor_type', $state)) ? GuarantorType::from($state['guarantor_type'])->label() : null)
                                    ->schema([
                                        Forms\Components\Select::make('guarantor_id')
                                            ->required()
                                            ->label('Guarantor')
                                            ->options(Guarantor::all()->pluck('name', 'id')),
                                        Forms\Components\TextInput::make('member_number')
                                            ->tel()
                                            ->required()
                                            ->maxLength(16),
                                        Forms\Components\DatePicker::make('valid_from'),
                                        Forms\Components\DatePicker::make('valid_to'),
                                        Forms\Components\Toggle::make('is_primary')
                                    ])
                            ]),
                    ])
                    ->contained(false)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient_code')
                    ->label('Patient Code')
                    ->prefix('#')
                    ->description(fn(Patient $record): string => $record->ref_patient_code ?? '')
                    ->searchable(['patient_code', 'ref_patient_code']),
                Tables\Columns\TextColumn::make('person.full_name')
                    ->label('Patient Name')
                    ->description(fn(Patient $record): string => $record->person->nickname ?? '')
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->orWhereHas('person', fn($q) => $q->where('full_name', 'like', "%{$search}%")
                            ->orWhere('nickname', 'like', "%{$search}%"));
                    }),
                Tables\Columns\TextColumn::make('person.gender')
                    ->label('Gender')
                    ->formatStateUsing(fn(Gender $state): string => $state->label())
                    ->badge()
                    ->icon(fn(Gender $state): string => $state->icon())
                    ->color(fn(Gender $state): string => $state->color())
                    ->placeholder('N/A'),
                Tables\Columns\TextColumn::make('person.date_of_birth')
                    ->label('Age')
                    ->suffix(' tahun')
                    ->formatStateUsing(function ($record) {
                        return self::calculateAge($record->person->date_of_birth, '%y');
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\IdentitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
            'view' => Pages\ViewPatient::route('/{record}'),
        ];
    }


    protected static function calculateAge(?string $state, $format = '%y tahun, %m bulan'): ?string
    {
        if (! $state) {
            return null;
        }

        $birthDate = Carbon::parse($state);
        // $age = $birthDate->age;
        $age = $birthDate->diff(Carbon::now())->format($format);

        return $age;
    }
}
