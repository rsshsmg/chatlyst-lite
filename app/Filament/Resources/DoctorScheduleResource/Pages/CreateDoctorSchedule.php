<?php

namespace App\Filament\Resources\DoctorScheduleResource\Pages;

use App\Filament\Resources\DoctorScheduleResource;
use App\Models\DoctorSchedule;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class CreateDoctorSchedule extends CreateRecord
{
    protected static string $resource = DoctorScheduleResource::class;

    /**
     * Holds base data for creating multiple day records when checkbox list provided.
     * @var array<int, mixed>
     */
    protected array $pendingDays = [];

    /**
     * Base payload for additional records (doctor_id, clinic_id, times)
     * @var array<string, mixed>
     */
    protected array $pendingBaseData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
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

        // If user selected multiple days via checkbox list, store pending days and base payload
        if (isset($data['day_of_week']) && is_array($data['day_of_week'])) {
            $this->pendingDays = $data['day_of_week'];
            // Keep base data for creating other records (remove day_of_week)
            $this->pendingBaseData = $data;
            unset($this->pendingBaseData['day_of_week']);

            // Set day_of_week to first selected so Filament will create one record normally
            $data['day_of_week'] = array_shift($this->pendingDays);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // If there are pending days, create additional schedule records
        if (! empty($this->pendingDays) && ! empty($this->pendingBaseData)) {
            foreach ($this->pendingDays as $day) {
                $payload = $this->pendingBaseData;
                $payload['day_of_week'] = $day;
                // Create additional schedules
                DoctorSchedule::create($payload);
            }
            // Clear pending arrays
            $this->pendingDays = [];
            $this->pendingBaseData = [];
        }
    }
}
