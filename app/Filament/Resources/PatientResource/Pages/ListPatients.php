<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPatients extends ListRecords
{
    protected static string $resource = PatientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('sync')->label('Sync SIMRS')->icon('heroicon-o-arrow-path')
                ->action(function () {
                    // Logic to sync with SIMRS
                    // This could be a call to a service that handles the synchronization
                    // For example: app('simrs.sync')->syncPatients();
                })
                ->requiresConfirmation()
                ->outlined()
                ->color('emerald'),
        ];
    }
}
