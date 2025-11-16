<?php

namespace App\Filament\Resources\DoctorResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SpecializationsRelationManager extends RelationManager
{
    protected static string $relationship = 'specializations';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('slug')
                ->required()
                ->maxLength(255),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            // Tables\Columns\TextColumn::make('slug')->searchable(),
        ])->filters([])->headerActions([
            Tables\Actions\AttachAction::make()->preloadRecordSelect(),
        ])->actions([
            Tables\Actions\DetachAction::make(),
        ])->bulkActions([
            Tables\Actions\DetachBulkAction::make(),
        ]);
    }
}
