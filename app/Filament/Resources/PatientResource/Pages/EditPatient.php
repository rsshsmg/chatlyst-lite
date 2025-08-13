<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Enums\AddressType;
use App\Enums\BloodType;
use App\Enums\ContactType;
use App\Enums\Gender;
use App\Enums\GuarantorType;
use App\Enums\IdentityType;
use App\Enums\MaritalStatus;
use App\Enums\RelationType;
use App\Enums\ReligionType;
use App\Filament\Resources\PatientResource;
use App\Models\Guarantor;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class EditPatient extends EditRecord
{
    protected static string $resource = PatientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return 'Profile';
    }


    public function form(Form $form): Form
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
                                                ->relationship('person')
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
                                                    Forms\Components\TextInput::make('place_of_birth')
                                                        ->maxLength(255),
                                                    Forms\Components\DatePicker::make('date_of_birth')
                                                        ->required()
                                                        ->reactive()
                                                        ->afterStateUpdated(fn(?string $state, Set $set) => $this->calculateAge($state, $set)),
                                                    Forms\Components\TextInput::make('age')
                                                        ->disabled()
                                                        ->afterStateHydrated(function (Get $get, Set $set) {
                                                            $this->calculateAge($get('date_of_birth'), $set);
                                                        }),
                                                    Forms\Components\Select::make('blood_type')
                                                        ->options(BloodType::array()),
                                                    Forms\Components\Select::make('religion')
                                                        ->options(ReligionType::array()),
                                                    Forms\Components\Select::make('marital_status')
                                                        ->options(MaritalStatus::array()),
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
                            ->badge(fn(?Model $record): int => $record->person?->identities()->count() ?? 0)
                            ->badgeColor(fn(?Model $record) => ($record->person?->identities()->count() ?? 0) > 0 ? 'success' : 'danger')
                            ->schema([
                                Forms\Components\Repeater::make('person')
                                    ->relationship('person.identities')
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
                            ->badge(fn(?Model $record): int => $record?->person?->addresses()->count() ?? 0)
                            ->badgeColor(fn(?Model $record) => ($record?->person?->addresses()->count() ?? 0) > 0 ? 'success' : 'danger')
                            ->schema([
                                Forms\Components\Repeater::make('person.addresses')
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
                        //     ->badge(fn(?Model $record): int => $record?->person?->contacts()->count() ?? 0)
                        //     ->badgeColor(fn(?Model $record) => ($record?->person?->contacts()->count() ?? 0) > 0 ? 'success' : 'danger')
                        //     ->schema([
                        //         Forms\Components\Repeater::make('contacts')
                        //             ->grid(2)
                        //             ->schema([
                        //                 Forms\Components\Select::make('relation_type')
                        //                     ->label('Hubungan')
                        //                     ->default(RelationType::SELF->value)
                        //                     ->options(RelationType::array())
                        //                     ->required(),

                        //                 Forms\Components\Toggle::make('is_primary')
                        //                     ->label('Kontak Utama')
                        //                     ->default(false),

                        //                 Forms\components\TextInput::make('notes')
                        //                     ->label('Catatan'),

                        //                 Forms\Components\Fieldset::make('Kontak')
                        //                     ->columns(1)
                        //                     ->schema([
                        //                         Forms\Components\Repeater::make('phones')
                        //                             ->simple(
                        //                                 PhoneInput::make('value')->label('Nomor Telepon'),
                        //                             )
                        //                             ->defaultItems(1),

                        //                         Forms\Components\Repeater::make('emails')
                        //                             ->simple(
                        //                                 Forms\Components\TextInput::make('value')->label('Alamat Email')->email(),
                        //                             )
                        //                             ->defaultItems(1),

                        //                         Forms\Components\Repeater::make('whatsapps')
                        //                             ->simple(
                        //                                 PhoneInput::make('value')->label('Nomor WhatsApp'),
                        //                             )
                        //                             ->defaultItems(1),
                        //                     ])
                        //             ])
                        //             ->label('Kontak Pasien')
                        //             ->addActionLabel('Tambah Kontak')
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
                    ->columnSpanFull()

            ]);
    }

    // protected function mutateFormDataBeforeFill(array $data): array
    // {
    //     $contacts = $this->record->contacts
    //         ->groupBy(fn($contact) => $contact->pivot->relation_type . '-' . $contact->pivot->is_primary)
    //         ->map(function ($groupedContacts) {
    //             $first = $groupedContacts->first();

    //             return [
    //                 'relation_type' => $first->pivot->relation_type,
    //                 'is_primary' => $first->pivot->is_primary,
    //                 'notes' => $first->pivot->notes,
    //                 'phones' => $groupedContacts->where('contact_type', ContactType::Phone->value)->map(fn($c) => $c->value)->values()->all(),
    //                 'emails' => $groupedContacts->where('contact_type', ContactType::Email->value)->map(fn($c) => $c->value)->values()->all(),
    //                 'whatsapps' => $groupedContacts->where('contact_type', ContactType::Whatsapp->value)->map(fn($c) => $c->value)->values()->all(),
    //             ];
    //         })->values()->all();

    //     $data['contacts'] = $contacts;

    //     return $data;
    // }

    private function calculateAge(?string $state, Set $set): void
    {
        if (! $state) {
            $set('age', null);
            return;
        }

        $birthDate = Carbon::parse($state);
        // $age = $birthDate->age;
        $age = $birthDate->diff(Carbon::now())->format('%y tahun, %m bulan');

        $set('age', $age);
    }
}
