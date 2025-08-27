<?php

namespace App\Filament\Resources;

use App\Enums\Gender;
use App\Filament\Exports\PhoneExporter;
use App\Filament\Resources\PhoneResource\Pages;
use App\Filament\Resources\PhoneResource\RelationManagers;
use App\Models\Phone;
use App\Models\Tag;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class PhoneResource extends Resource
{
    protected static ?string $model = Phone::class;

    protected static ?string $navigationIcon = 'icon-whatsapp';

    protected static ?string $modelLabel = 'Export Phone Numbers';

    public static function getNavigationGroup(): ?string
    {
        return 'Interactions';
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
                    ->icon(fn(string $state, Phone $record): string => match (phone($state, $record->country_code)->isValid()) {
                        true => 'heroicon-o-check-circle',
                        false => 'heroicon-o-exclamation-triangle',
                    })
                    ->iconColor(fn(string $state, Phone $record): string => match (phone($state, $record->country_code)->isValid()) {
                        true => 'success',
                        false => 'warning',
                    })
                    ->color(fn(string $state, Phone $record): string => match (phone($state, $record->country_code)->isValid()) {
                        true => '',
                        false => 'warning',
                    })
                    ->tooltip(fn(string $state, Phone $record): string => match (phone($state, $record->country_code)->isValid()) {
                        true => '',
                        false => 'Invalid phone number',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('person.full_name')
                    ->label('Full Name')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('person.age')
                //     ->label('Age')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('country_code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('person.gender')
                    ->label('Gender')
                    ->formatStateUsing(fn(Gender $state): string => $state->label())
                    ->searchable(),
                Tables\Columns\TextColumn::make('person.age')
                    ->label('Age (years)')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        if (!is_numeric($search)) {
                            return $query; // kalau input bukan angka, skip
                        }

                        $search = (int) $search;
                        $searchDate = Carbon::now()->subYears($search)->format('Y-m-d');

                        return $query->whereHas('person', function (Builder $subquery) use ($searchDate) {
                            $subquery->whereDate('date_of_birth', $searchDate);
                        });
                    }),
                // Tables\Columns\IconColumn::make('is_whatsapp')
                //     ->label('Channel')
                //     ->boolean()
                //     ->tooltip(fn($state): string => match ($state) {
                //         true => 'WhatsApp available',
                //         false => 'Phone/SMS only',
                //     })
                //     ->trueIcon('icon-whatsapp')
                //     ->trueColor('success')
                //     ->falseIcon('icon-mobile-screen')
                //     ->falseColor('gray'),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                // SelectFilter::make('tag')
                //     ->multiple()
                //     ->options(function () {
                //         return Tag::orderBy('name')->limit(15)->pluck('name', 'id');
                //     })
                //     ->getSearchResultsUsing(function (string $search) {
                //         return Tag::where('name', 'like', "%{$search}%")
                //             ->limit(50)
                //             ->pluck('name', 'id');
                //     })
                //     ->query(function ($query, $data) {
                //         $tagIds = $data['values'] ?? [];
                //         if (empty($tagIds)) {
                //             return $query;
                //         }

                //         return $query->where(function ($query) use ($tagIds) {
                //             $query
                //                 ->whereHas('person.patient.tags', function ($q) use ($tagIds) {
                //                     $q->whereIn('tags.id', $tagIds);
                //                 })
                //                 ->orWhereHas('person.tags', function ($q) use ($tagIds) {
                //                     $q->whereIn('tags.id', $tagIds);
                //                 });
                //         });
                //     })
                //     ->columnSpanFull(),
                SelectFilter::make('person.gender')
                    ->label('Gender')
                    ->options(Gender::array())
                    ->query(function ($query, $data) {
                        $gender = $data['value'] ?? [];

                        if (empty($gender)) {
                            return $query;
                        }
                        return $query->whereHas('person', function ($q) use ($gender) {
                            $q->where('gender', $gender);
                        });
                    }),
                Filter::make('age')
                    ->form([
                        Forms\Components\Select::make('operator')
                            ->options([
                                '=' => '=',
                                '>' => '>',
                                '<' => '<',
                                'range' => 'Range',
                            ])
                            ->required()
                            ->label('Operator'),

                        Forms\Components\TextInput::make('age')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(120)
                            ->label('From'),

                        Forms\Components\TextInput::make('max_age')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(120)
                            ->label('To')
                            ->visible(fn($get) => $get('operator') === 'range'),
                    ])
                    ->columns(3)
                    ->query(function ($query, array $data) {
                        if (empty($data['operator']) || empty($data['age'])) {
                            return $query;
                        }

                        $operator = $data['operator'];
                        $age = (int) $data['age'];

                        return $query->whereHas('person', function ($personQuery) use ($operator, $age, $data) {
                            $now = Carbon::now();

                            if ($operator === '=') {
                                // Exact age
                                $personQuery->whereBetween('date_of_birth', [
                                    $now->copy()->subYears($age)->startOfYear(),
                                    $now->copy()->subYears($age)->endOfYear(),
                                ]);
                            }

                            if ($operator === '>') {
                                // Older than input age
                                $personQuery->whereDate('date_of_birth', '<=', $now->copy()->subYears($age));
                            }

                            if ($operator === '<') {
                                // Younger than input age
                                $personQuery->whereDate('date_of_birth', '>=', $now->copy()->subYears($age));
                            }

                            if ($operator === 'range' && !empty($data['max_age'])) {
                                $maxAge = (int) $data['max_age'];

                                // Between min and max age range
                                $personQuery->whereBetween('date_of_birth', [
                                    $now->copy()->subYears($maxAge)->startOfDay(),
                                    $now->copy()->subYears($age)->endOfDay(),
                                ]);
                            }
                        });
                    })
                    ->columnSpan(2),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormSchema(fn(array $filters): array => [
                $filters['person.gender'],
                Fieldset::make('Age (years)')
                    ->schema([
                        $filters['age'],
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
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
                    // Tables\Actions\ExportAction::make()
                    //     ->label('Export All')
                    //     ->exporter(PhoneExporter::class)
                    //     ->columnMapping(false),
                    Tables\Actions\ExportAction::make()
                        ->label('Export All (Valid only)')
                        ->exporter(PhoneExporter::class)
                        ->columnMapping(false)
                        ->modifyQueryUsing(fn(Builder $query) => $query->where('number', 'REGEXP', '^\\+?[1-9][0-9]{7,14}$')),
                ])
                    ->label('Export')
                    ->button()
                    ->outlined()
                    ->color('emerald')
                    ->icon('heroicon-m-chevron-down')
                    ->iconPosition(IconPosition::After),
            ])
            ->deferLoading();
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
