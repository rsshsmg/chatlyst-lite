<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorLeaveResource\Pages;
use App\Models\DoctorLeave;
use App\Models\Doctor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DoctorLeaveResource extends Resource
{
    protected static ?string $model = DoctorLeave::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    public static function getNavigationGroup(): ?string
    {
        return 'Appointments';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('doctor_id')->label('Doctor')->options(Doctor::all()->pluck('name', 'id'))->required(),
            Forms\Components\DatePicker::make('start_date')->required(),
            Forms\Components\DatePicker::make('end_date'),
            Forms\Components\Textarea::make('reason'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('doctor.name')->label('Doctor')->searchable(),
            Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
            Tables\Columns\TextColumn::make('end_date')->date()->sortable(),
            Tables\Columns\TextColumn::make('reason')->limit(50),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDoctorLeaves::route('/'),
            'create' => Pages\CreateDoctorLeave::route('/create'),
            'edit' => Pages\EditDoctorLeave::route('/{record}/edit'),
        ];
    }
}
