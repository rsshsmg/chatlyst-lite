<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use App\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission as ModelsPermission;

class PermissionResource extends Resource
{
    protected static ?string $model = ModelsPermission::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return config('filament-users.group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->readOnly(fn(Page $livewire) => $livewire instanceof EditRecord)
                    ->disabled(fn(Page $livewire) => $livewire instanceof EditRecord),
                Forms\Components\Select::make('guard_name')
                    ->options(
                        collect(array_keys(config('auth.guards')))
                            ->mapWithKeys(fn($guard) => [$guard => Str::headline($guard)])
                            ->toArray()
                    ),
                Forms\Components\TextInput::make('description')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->description(fn(ModelsPermission $record): ?string => $record->description),
                Tables\Columns\TextColumn::make('guard_name')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
