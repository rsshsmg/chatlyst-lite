<?php

namespace App\Filament\Resources\PersonResource\Pages;

use App\Filament\Resources\PersonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPerson extends EditRecord
{
    protected static string $resource = PersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('sync')
                ->label('Sync Person')
                ->icon('heroicon-o-arrow-path')
                ->tooltip('Sync patient data with SIMRS')
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
