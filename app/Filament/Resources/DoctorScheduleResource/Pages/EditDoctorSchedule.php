<?php

namespace App\Filament\Resources\DoctorScheduleResource\Pages;

use App\Filament\Resources\DoctorScheduleResource;
use App\Models\Doctor;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class EditDoctorSchedule extends EditRecord
{
    protected static string $resource = DoctorScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('doctor_id')->label('Doctor')->options(Doctor::all()->pluck('name', 'id'))->required(),
            Forms\Components\Select::make('clinic_id')->label('Clinic')
                ->relationship('clinic', 'name')
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
            Forms\Components\Select::make('day_of_week')
                ->options(\App\Enums\DayOfWeek::options())
                ->required()
                ->disabled(),
        ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['start_time']) && ! empty($data['end_time'])) {
            $start = Carbon::parse($data['start_time']);
            $end = Carbon::parse($data['end_time']);
            if (! $end->greaterThan($start)) {
                throw ValidationException::withMessages([
                    'end_time' => 'End time must be after start time.',
                ]);
            }
        }

        return $data;
    }
}
