<?php

namespace App\Filament\Resources;

use App\Filament\Exports\PhoneExporter;
use App\Filament\Resources\PhoneResource\Pages;
use App\Filament\Resources\PhoneResource\RelationManagers;
use App\Models\Phone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class PhoneResource extends Resource
{
    protected static ?string $model = Phone::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';

    public static function getNavigationGroup(): ?string
    {
        return 'Customers';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('person_id')
                    ->relationship(name: 'person', titleAttribute: 'full_name')
                    ->required()
                    ->searchable(),
                PhoneInput::make('number')
                    ->required()
                    ->defaultCountry('ID')
                    ->countryStatePath('country_code'),
                // Forms\Components\TextInput::make('country_code')
                //     ->required()
                //     ->maxLength(3)
                //     ->default('ID'),
                Forms\Components\Toggle::make('is_whatsapp')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                PhoneColumn::make('number')
                    ->displayFormat(PhoneInputNumberType::E164)
                    ->countryColumn('country_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('person.full_name')
                    ->label('Full Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country_code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_whatsapp')
                    ->label('Channel')
                    ->boolean()
                    ->tooltip(fn($state): string => match ($state) {
                        true => 'WhatsApp available',
                        false => 'Phone/SMS only',
                    })
                    ->trueIcon('icon-whatsapp')
                    ->trueColor('success')
                    ->falseIcon('icon-mobile-screen')
                    ->falseColor('gray'),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean(),
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
            ])
            ->recordUrl(fn() => null)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ExportAction::make()
                        ->label('Export All (Valid)')
                        ->exporter(PhoneExporter::class)
                        ->modifyQueryUsing(fn(Builder $query) => $query->where('number', 'REGEXP', '^\\+?[1-9][0-9]{7,14}$')),
                    Tables\Actions\ExportAction::make()
                        ->label('Export WhatsApp Only')
                        ->exporter(PhoneExporter::class)
                        ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('verified_at')->where('is_whatsapp', true)),
                ])
                    ->label('Export')
                    ->button()
                    ->color('gray')
                    ->icon('heroicon-m-chevron-down')
                    ->iconPosition(IconPosition::After),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPhones::route('/'),
            'create' => Pages\CreatePhone::route('/create'),
            'edit' => Pages\EditPhone::route('/{record}/edit'),
        ];
    }
}
