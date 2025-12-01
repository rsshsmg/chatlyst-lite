<?php

namespace App\Filament\Resources\DoctorResource\RelationManagers;

use App\Models\Doctor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';

    protected static ?string $recordTitleAttribute = 'day_of_week';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('clinic_id')->label('Clinic')
                ->relationship('clinic', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->columnSpanFull(),
            // Forms\Components\Select::make('day_of_week')
            //     ->options(\App\Enums\DayOfWeek::options())
            //     ->required()
            //     ->columnSpanFull(),
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
            Forms\Components\CheckboxList::make('day_of_week')
                ->options(\App\Enums\DayOfWeek::options())
                ->required()
                ->columns(6)
                ->columnSpanFull()
                ->gridDirection('column'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('clinic.name')->label('Clinic')->searchable(),
            Tables\Columns\TextColumn::make('day_of_week')
                ->sortable()
                ->formatStateUsing(fn($state) => (
                    $state instanceof \App\Enums\DayOfWeek
                    ? $state->label()
                    : (\App\Enums\DayOfWeek::tryFrom($state)?->label() ?? $state)
                )),
            Tables\Columns\TextColumn::make('start_time')->label('Start')->dateTime('H:i'),
            Tables\Columns\TextColumn::make('end_time')->label('End')->dateTime('H:i'),
        ])->filters([])->headerActions([
            Tables\Actions\CreateAction::make()
                ->action(function (array $data) {
                    // If multiple days selected (checkbox list), create a schedule per day
                    $days = $data['day_of_week'] ?? null;

                    // Ensure we have the relationship instance to create children
                    $relation = $this->getRelationship();

                    // normalize days to array
                    $daysArr = is_array($days) ? $days : ($days !== null ? [$days] : []);

                    // prepare base payload (relation will auto-fill FK to doctor)
                    $base = $data;
                    unset($base['day_of_week']);

                    // collect payloads to create
                    $toCreate = [];
                    foreach ($daysArr as $day) {
                        $payload = array_merge($base, ['day_of_week' => $day]);

                        // Prevent duplicate exact schedule (same day, start, end)
                        $exists = $relation->where('day_of_week', $day)
                            ->where('start_time', $payload['start_time'])
                            ->where('end_time', $payload['end_time'])
                            ->exists();

                        if (! $exists) {
                            $toCreate[] = $payload;
                        }
                    }

                    if (empty($toCreate)) {
                        // Nothing to create - maybe all selected slots already exist
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'day_of_week' => 'No new schedules to create. The selected day/time combinations already exist.',
                        ]);
                    }

                    // Use createMany to insert all payloads in one go
                    if (method_exists($relation, 'createMany')) {
                        $relation->createMany($toCreate);
                    } else {
                        foreach ($toCreate as $payload) {
                            $relation->create($payload);
                        }
                    }
                }),
        ])->actions([
            Tables\Actions\EditAction::make()
                ->form([
                    Forms\Components\Grid::make()
                        ->columns(2)
                        ->schema([
                            // Forms\Components\Select::make('doctor_id')
                            //     ->label('Doctor')
                            //     ->options(Doctor::all()->pluck('name', 'id'))
                            //     ->required()
                            //     ->disabled(),
                            Forms\Components\Select::make('day_of_week')
                                ->options(\App\Enums\DayOfWeek::options())
                                ->required()
                                ->columnSpanFull()
                                ->disabled(),
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
                        ])
                ]),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ])->defaultSort('day_of_week', 'asc');
    }
}
