<?php

namespace App\Filament\Resources\DoctorResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';

    protected static ?string $recordTitleAttribute = 'day_of_week';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('day_of_week')
                ->options(\App\Enums\DayOfWeek::options())
                ->required(),
            Forms\Components\TimePicker::make('start_time')
                ->required()
                ->minutesStep(15)
                ->datalist([
                    '08:00',
                    '08:30',
                    '09:00',
                    '09:30',
                    '10:00',
                    '10:30',
                    '11:00',
                    '11:30',
                    '12:00',
                    '12:30',
                    '13:00',
                    '13:30',
                    '14:00',
                    '14:30',
                    '15:00',
                    '15:30',
                    '16:00',
                    '16:30',
                    '17:00',
                    '17:30',
                    '18:00',
                    '18:30',
                    '19:00',
                    '19:30',
                    '20:00',
                    '20:30',
                ])
                ->seconds(false),
            Forms\Components\TimePicker::make('end_time')
                ->required()
                ->minutesStep(15)
                ->datalist([
                    '08:00',
                    '08:30',
                    '09:00',
                    '09:30',
                    '10:00',
                    '10:30',
                    '11:00',
                    '11:30',
                    '12:00',
                    '12:30',
                    '13:00',
                    '13:30',
                    '14:00',
                    '14:30',
                    '15:00',
                    '15:30',
                    '16:00',
                    '16:30',
                    '17:00',
                    '17:30',
                    '18:00',
                    '18:30',
                    '19:00',
                    '19:30',
                    '20:00',
                    '20:30',
                ])
                ->seconds(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('day_of_week')
                ->sortable()
                ->formatStateUsing(fn($state) => (
                    $state instanceof \App\Enums\DayOfWeek
                    ? $state->label()
                    : (\App\Enums\DayOfWeek::tryFrom($state)?->label() ?? $state)
                )),
            Tables\Columns\TextColumn::make('start_time')->label('Start')->dateTime('H:i'),
            Tables\Columns\TextColumn::make('end_time')->label('End')->dateTime('H:i'),
        ])->filters([])->headerActions([
            Tables\Actions\CreateAction::make(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }
}
