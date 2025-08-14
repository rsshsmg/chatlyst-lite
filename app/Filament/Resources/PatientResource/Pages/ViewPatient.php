<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Enums\AddressType;
use App\Enums\BloodType;
use App\Enums\Gender;
use App\Enums\IdentityType;
use App\Enums\MaritalStatus;
use App\Enums\RelationType;
use App\Enums\ReligionType;
use App\Filament\Resources\PatientResource;
use App\Models\Address;
use App\Models\Identity;
use App\Models\Person;
use App\Models\Phone;
use App\Models\SubDistrict;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Components\SpatieTagsEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Model;
use libphonenumber\PhoneNumberType;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class ViewPatient extends ViewRecord
{
    protected static string $resource = PatientResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Split::make([
                    Infolists\Components\Tabs::make('Tabs')
                        // ->columns(4)
                        ->schema([
                            Infolists\Components\Tabs\Tab::make('General')
                                ->columns(4)
                                ->schema([
                                    Infolists\Components\TextEntry::make('person.full_name')
                                        ->label('Full Name')
                                        ->columnSpan(2),
                                    Infolists\Components\TextEntry::make('person.nickname')
                                        ->label('Nickname'),
                                    Infolists\Components\TextEntry::make('person.gender')
                                        ->label('Gender')
                                        ->badge()
                                        ->formatStateUsing(fn(Gender $state): string => $state->label())
                                        ->icon(fn(Gender $state): string => $state->icon())
                                        ->color(fn(Gender $state): string => $state->color()),
                                    Infolists\Components\TextEntry::make('person.place_of_birth')
                                        ->label('Place of Birth'),
                                    Infolists\Components\TextEntry::make('person.date_of_birth')
                                        ->label('Date of Birth')
                                        ->date(),
                                    Infolists\Components\TextEntry::make('age')
                                        ->state(function (?Model $record): string {
                                            return $this->calculateAge($record->person->date_of_birth);
                                        })
                                        ->badge()
                                        ->color('info'),
                                    Infolists\Components\TextEntry::make('person.blood_type')
                                        ->label('Blood Type')
                                        ->formatStateUsing(fn(BloodType $state): string => $state->label()),
                                    Infolists\Components\TextEntry::make('person.marital_status')
                                        ->label('Marital Status')
                                        ->formatStateUsing(fn(MaritalStatus $state): string => $state->label()),
                                    Infolists\Components\TextEntry::make('person.religion')
                                        ->label('Religion')
                                        ->formatStateUsing(fn(ReligionType $state): string => $state->label()),
                                    Infolists\Components\TextEntry::make('person.education.name')
                                        ->label('Education')
                                        ->placeholder('N/A'),
                                    Infolists\Components\TextEntry::make('person.job_title.name')
                                        ->label('Job Title')
                                        ->placeholder('N/A'),
                                    Infolists\Components\TextEntry::make('person.nationality')
                                        ->label('Nationality'),
                                ]),
                            Infolists\Components\Tabs\Tab::make('Identities')
                                ->badge(fn(?Model $record): int => $record?->person->identities()->count() ?? 0)
                                ->badgeColor(fn(?Model $record) => ($record?->person->identities()->count() ?? 0) > 0 ? 'success' : 'danger')
                                ->schema([
                                    Infolists\Components\RepeatableEntry::make('person.identities')
                                        ->label('Patient Identities')
                                        ->placeholder('N/A')
                                        ->columns(4)
                                        ->grid(2)
                                        ->schema([
                                            // Infolists\Components\TextEntry::make('is_primary')
                                            //     ->label('')
                                            //     ->badge()
                                            //     ->formatStateUsing(fn(int $state): string => $state ? 'primary' : 'alt')
                                            //     ->hint(fn(int $state): string => $state ? 'Primary identity' : 'Alternative identity')
                                            //     ->color(fn(int $state): string => $state ? 'success' : 'gray')
                                            //     ->columnSpanFull(),
                                            Infolists\Components\TextEntry::make('number')
                                                ->label('')
                                                ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                                ->weight(FontWeight::SemiBold)
                                                ->placeholder('N/A')
                                                // ->prefix(fn(?Identity $currentRecord): string => ($currentRecord) ? $currentRecord->identity_type->label() . ' - ' : '')
                                                ->columnSpan(3),
                                            Infolists\Components\TextEntry::make('identity_type')
                                                ->label('')
                                                ->formatStateUsing(fn(IdentityType $state): string => $state->label())
                                                ->color(fn(Identity $currentState): string => $currentState->is_primary ? 'info' : 'gray')
                                                ->icon(fn(Identity $currentState): string => $currentState->is_primary ? 'icon-pin-fill' : '')
                                                ->tooltip(fn(Identity $currentState): string => $currentState->is_primary ? 'Primary' : 'Alternative')
                                                ->badge()
                                                ->alignEnd(),
                                            Infolists\Components\TextEntry::make('issued_at')
                                                ->date()
                                                ->placeholder('N/A')
                                                ->columnSpan(2),
                                            Infolists\Components\TextEntry::make('expired_at')
                                                ->date()
                                                ->placeholder('N/A')
                                                ->columnSpan(2),
                                        ]),
                                ]),
                            Infolists\Components\Tabs\Tab::make('Contact')
                                ->schema([
                                    Infolists\Components\Section::make('Phones')
                                        ->aside()
                                        ->schema(function ($record) {
                                            return $record->person->phones->map(function ($phone) {
                                                $isValid = phone($phone->number, $phone->country_code)->isValid();

                                                $isValidTooltip = ($isValid) ? null : 'Invalid phone number';
                                                $isValidColor = ($isValid) ? null : 'warning';
                                                $isPrimaryTooltip = ($phone->is_primary) ? 'Primary Phone' : null;
                                                $isPrimaryIcon = $phone->is_primary ? 'heroicon-s-square-2-stack' : 'heroicon-o-square-2-stack';

                                                $info = [$isPrimaryTooltip, $isValidTooltip];

                                                $tooltip = (count(array_filter($info)) > 1) ? implode(' - ', $info) : $info[0];

                                                return PhoneEntry::make("phone_{$phone->id}")
                                                    ->state($phone->number)
                                                    ->countryColumn('country_code')
                                                    ->hiddenLabel()
                                                    // ->icon(fn(string $state, Phone $record): string => match (phone($state, $record->country_code)->isValid()) {
                                                    //     true => 'heroicon-o-check-circle',
                                                    //     false => 'heroicon-o-exclamation-triangle',
                                                    // })
                                                    ->color($isValidColor)
                                                    ->placeholder('N/A')
                                                    ->icon($isPrimaryIcon)
                                                    ->iconColor($phone->is_primary ? 'info' : 'gray')
                                                    ->tooltip($tooltip)
                                                    ->suffixAction(
                                                        Infolists\Components\Actions\Action::make("edit_{$phone->id}")
                                                            ->label("Edit Patient Phone")
                                                            ->icon('heroicon-o-pencil')
                                                            ->form([
                                                                PhoneInput::make('number')
                                                                    ->countryStatePath('country_code')
                                                                    ->strictMode(true)
                                                                    // ->separateDialCode(true)
                                                                    ->displayNumberFormat(PhoneInputNumberType::E164)
                                                                    ->inputNumberFormat(PhoneInputNumberType::E164)
                                                                    ->formatOnDisplay(true)
                                                                    ->defaultCountry(env('APP_DEFAULT_COUNTRY', 'ID'))
                                                                    ->label('Phone Number')
                                                                    ->default($phone->number)
                                                                    ->required(),
                                                                Forms\Components\Toggle::make('is_whatsapp')
                                                                    ->label('WhatsApp Active?')
                                                                    ->default($phone->is_whatsapp)
                                                                    ->inlineLabel(),
                                                                Forms\Components\Toggle::make('is_primary')
                                                                    ->label('Primary?')
                                                                    ->default($phone->is_primary)
                                                                    ->inlineLabel(),
                                                                Forms\Components\Toggle::make('is_active')
                                                                    ->label('Active?')
                                                                    ->default($phone->is_active)
                                                                    ->inlineLabel(),
                                                            ])
                                                            ->action(function (array $data) use ($phone) {
                                                                $act = $phone->update([
                                                                    'number' => $data['number'],
                                                                    'country_code' => $data['country_code'],
                                                                    'is_whatsapp' => $data['is_whatsapp'],
                                                                    'is_primary' => $data['is_primary'],
                                                                    'is_active' => $data['is_active'],
                                                                ]);

                                                                $this->refreshFormData([
                                                                    'number',
                                                                    'is_primary',
                                                                    'is_active'
                                                                ]);

                                                                return $act;
                                                            })
                                                    );
                                            })->toArray();
                                        }),
                                    Infolists\Components\Section::make('Emails')
                                        ->aside()
                                        ->schema(function ($record) {
                                            return $record->person->emails->map(function ($email) {
                                                return Infolists\Components\TextEntry::make('email')
                                                    ->label('')
                                                    ->placeholder('N/A')
                                                    ->default($email->email)
                                                    ->icon($email->is_verified ? 'heroicon-o-envelope' : 'heroicon-o-envelope')
                                                    ->iconColor($email->is_verified ? 'success' : 'gray')
                                                    ->tooltip($email->is_verified ? 'Primary Email' : 'Alternative Email')
                                                    ->suffixAction(
                                                        Infolists\Components\Actions\Action::make("edit_{$email->id}")
                                                            ->label("Edit Patient Email")
                                                            ->icon('heroicon-o-pencil')
                                                            ->form([
                                                                Forms\Components\TextInput::make('number')
                                                                    ->label('Email Address')
                                                                    ->default($email->email)
                                                                    ->required(),
                                                            ])
                                                            ->action(fn(array $data) => $email->update([
                                                                'email' => $data['email'],
                                                            ]))
                                                    );
                                            })->toArray();
                                        }),
                                ]),
                            Infolists\Components\Tabs\Tab::make('Address')
                                ->badge(fn(?Model $record): int => $record?->person->addresses()->count() ?? 0)
                                ->badgeColor(fn(?Model $record) => ($record?->person->addresses()->count() ?? 0) > 0 ? 'success' : 'danger')
                                ->schema([
                                    Infolists\Components\RepeatableEntry::make('person.addresses')
                                        ->label('Patient Addresses')
                                        ->placeholder('N/A')
                                        ->grid(2)
                                        ->schema([
                                            // Infolists\Components\TextEntry::make('is_primary')
                                            //     ->label('')
                                            //     ->badge()
                                            //     ->formatStateUsing(fn(int $state): string => $state ? 'Legal' : 'Domicile')
                                            //     ->tooltip(fn(int $state): string => $state ? 'Legal address' : 'Domicile address')
                                            //     ->color(fn(int $state): string => $state ? 'success' : 'gray'),
                                            Infolists\Components\TextEntry::make('address_type')
                                                ->label('')
                                                ->badge()
                                                ->formatStateUsing(fn(AddressType $state): string => $state->label())
                                                ->color(fn(Address $currentState): string => $currentState->is_primary ? 'info' : 'gray')
                                                ->icon(fn(Address $currentState): string => $currentState->is_primary ? 'icon-pin-fill' : ''),
                                            Infolists\Components\TextEntry::make('address')
                                                ->label('')
                                                ->inlineLabel()
                                                ->placeholder('N/A')
                                                ->size(Infolists\Components\TextEntry\TextEntrySize::Medium),
                                            Infolists\Components\TextEntry::make('subdistrict')
                                                ->label('')
                                                ->formatStateUsing(fn(SubDistrict $state): string => implode(', ', [
                                                    $state->name,
                                                    $state->district->name,
                                                    $state->district->regency->name,
                                                    $state->district->regency->province->name,
                                                    $state->district->regency->province->country->name
                                                ]))
                                                ->inlineLabel(),
                                            Infolists\Components\TextEntry::make('postal_code')
                                                ->label('Kodepos')
                                                ->inlineLabel(),
                                        ]),
                                ]),
                        ]),
                    Infolists\Components\Section::make('Patient Information')
                        ->schema([
                            Infolists\Components\TextEntry::make('patient_code')
                                ->label('Patient Code'),
                            Infolists\Components\TextEntry::make('ref_patient_code')
                                ->label('Ref Patient Code')
                                ->placeholder('N/A'),
                            Infolists\Components\TextEntry::make('created_at')
                                ->dateTime()
                                ->label('Registered At'),
                            SpatieTagsEntry::make('tags')
                                ->label('Tags')
                                ->iconPosition(IconPosition::Before)
                                ->placeholder('Add tags...'),
                        ])
                        ->grow(false),
                ])
                    ->columnSpanFull(),
                Infolists\Components\RepeatableEntry::make('guardians')
                    ->label('Patient Guardians')
                    ->placeholder('N/A')
                    ->columnSpanFull()
                    ->columns(2)
                    ->grid(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('pivot.relation_type')
                            ->label('Relation Type')
                            ->columnSpanFull()
                            ->formatStateUsing(fn($state): string => RelationType::from($state)->label()),
                        // ->badge()
                        // ->color(fn($state): string => $state->color())
                        // ->icon(fn($state): string => $state->icon()),
                        Infolists\Components\TextEntry::make('full_name')
                            ->columnSpanFull(),
                        Infolists\Components\RepeatableEntry::make('phones')
                            ->label('Phones')
                            ->placeholder('N/A')
                            ->contained(false)
                            ->schema([
                                PhoneEntry::make('number')
                                    ->label('')
                                    ->countryColumn('country_code')
                                    // ->defaultCountry($phone->country_code)
                                    // ->displayFormat(PhoneInputNumberType::E164)
                                    // ->formatStateUsing(function (string $state) use ($phone) {
                                    //     return phone(
                                    //         number: $state,
                                    //         country: $phone->country_code,
                                    //         format: PhoneInputNumberType::E164->toLibPhoneNumberFormat()
                                    //     );
                                    // })
                                    ->icon(fn(Phone $phone): string => $phone->is_primary ? 'icon-mobile-screen' : 'icon-mobile-screen')
                                    ->iconColor(fn(Phone $phone): string => $phone->is_primary ? 'success' : 'gray')
                                    ->hintIconTooltip(fn(Phone $phone): string => $phone->is_primary ? 'Primary Phone' : 'Alternative Phone'),
                            ]),
                        Infolists\Components\RepeatableEntry::make('emails')
                            ->label('Emails')
                            ->placeholder('N/A')
                            ->contained(false)
                            ->schema([
                                Infolists\Components\TextEntry::make('email')
                                    ->label('')
                                    ->placeholder('N/A')
                                    ->icon(fn($email): string => $email->is_verified ? 'heroicon-o-envelope' : 'heroicon-o-envelope')
                                    ->iconColor(fn($email): string => $email->is_verified ? 'success' : 'gray')
                                    ->hintIconTooltip(fn($email): string => $email->is_verified ? 'Primary Email' : 'Alternative Email'),
                            ]),
                        Infolists\Components\TextEntry::make('pivot.note')
                            ->label('Note')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
            ]);
    }

    private function calculateAge(?string $state): ?string
    {
        if (! $state) {
            return null;
        }

        $birthDate = Carbon::parse($state);
        // $age = $birthDate->age;
        return $birthDate->diff(Carbon::now())->format('%y tahun, %m bulan');
    }
}
