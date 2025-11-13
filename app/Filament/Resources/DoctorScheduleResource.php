<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorScheduleResource\Pages;
use App\Models\DoctorSchedule;
use App\Models\Doctor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DoctorScheduleResource extends Resource
{
    protected static ?string $model = DoctorSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function getNavigationGroup(): ?string
    {
        return 'Appointments';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('doctor_id')->label('Doctor')->options(Doctor::all()->pluck('name', 'id'))->required(),
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

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('doctor.name')->label('Doctor')->searchable(),
            Tables\Columns\TextColumn::make('day_of_week')
                ->sortable()
                ->formatStateUsing(fn($state) => (
                    $state instanceof \App\Enums\DayOfWeek
                    ? $state->label()
                    : (\App\Enums\DayOfWeek::tryFrom($state)?->label() ?? $state)
                )),
            Tables\Columns\TextColumn::make('start_time')->label('Start')->dateTime('H:i'),
            Tables\Columns\TextColumn::make('end_time')->label('End')->dateTime('H:i'),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDoctorSchedules::route('/'),
            'create' => Pages\CreateDoctorSchedule::route('/create'),
            'edit' => Pages\EditDoctorSchedule::route('/{record}/edit'),
        ];
    }
}
