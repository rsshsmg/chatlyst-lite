<?php

namespace App\Filament\Resources\DoctorLeaveResource\Pages;

use App\Filament\Resources\DoctorLeaveResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditDoctorLeave extends EditRecord
{
    protected static string $resource = DoctorLeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
