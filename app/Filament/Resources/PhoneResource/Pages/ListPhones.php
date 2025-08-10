<?php

namespace App\Filament\Resources\PhoneResource\Pages;

use App\Filament\Exports\PhoneExporter;
use App\Filament\Resources\PhoneResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPhones extends ListRecords
{
    protected static string $resource = PhoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
