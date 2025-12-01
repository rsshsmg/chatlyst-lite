<?php

namespace App\Filament\Resources\DoctorLeaveResource\Pages;

use App\Filament\Resources\DoctorLeaveResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListDoctorLeaves extends ListRecords
{
    protected static string $resource = DoctorLeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
