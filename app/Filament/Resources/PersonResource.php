<?php

namespace App\Filament\Resources;

use App\Enums\BloodType;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\ReligionType;
use App\Filament\Resources\PersonResource\Pages;
use App\Filament\Resources\PersonResource\RelationManagers;
use App\Models\Education;
use App\Models\Person;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'icon-person-bounding-box';

    protected static ?string $modelLabel = 'Profile';

    public static function getNavigationGroup(): ?string
    {
        return 'Customers';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('full_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nickname')
                    ->maxLength(255),
                Forms\Components\Select::make('gender')
                    ->options(Gender::array())
                    ->required(),
                Forms\Components\TextInput::make('place_of_birth')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date_of_birth'),
                Forms\Components\TextInput::make('mother_name')
                    ->maxLength(255),
                Forms\Components\Select::make('blood_type')
                    ->options(BloodType::array()),
                Forms\Components\Select::make('religion')
                    ->options(ReligionType::array()),
                Forms\Components\Select::make('marital_status')
                    ->options(MaritalStatus::array()),
                Forms\Components\Select::make('education_id')
                    ->options(Education::query()->pluck('name', 'id')),
                Forms\Components\Select::make('job_title_id')
                    ->options(Education::query()->pluck('name', 'id')),
                Forms\Components\TextInput::make('lang_code')
                    ->maxLength(6)
                    ->default('id_ID'),
                Forms\Components\TextInput::make('ethnicity_code'),
                Forms\Components\Toggle::make('is_foreigner')
                    ->default(false)
                    ->required(),
                Forms\Components\TextInput::make('nationality')
                    ->maxLength(255)
                    ->default('ID'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('id')
                //     ->label('ID')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->description(fn(Person $record): string => $record->nickname ?? '')
                    ->searchable(['full_name', 'nickname']),
                Tables\Columns\TextColumn::make('gender')
                    ->formatStateUsing(fn(Gender $state): string => $state->label())
                    ->badge()
                    ->icon(fn(Gender $state): string => $state->icon())
                    ->color(fn(Gender $state): string => $state->color()),
                Tables\Columns\TextColumn::make('age')
                    ->suffix(' tahun')
                    ->placeholder('N/A'),
                Tables\Columns\TextColumn::make('tags.name')
                    ->badge()
                    ->separator(',')
                    ->limitList(3),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
        ];
    }
}
