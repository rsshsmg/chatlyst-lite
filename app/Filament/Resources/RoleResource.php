<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models\Role;
use BezhanSalleh\FilamentShield\Forms\ShieldSelectAllToggle;
use BezhanSalleh\FilamentShield\Resources\RoleResource as ResourcesRoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use Spatie\Permission\Models\Role as ModelsRole;

class RoleResource extends ResourcesRoleResource
{
    use HasPageShield;

    protected static ?string $model = ModelsRole::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return config('filament-users.group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('filament-shield::filament-shield.field.name'))
                                    ->unique(
                                        ignoreRecord: true,
                                        /** @phpstan-ignore-next-line */
                                        modifyRuleUsing: fn(Unique $rule) => Utils::isTenancyEnabled() ? $rule->where(Utils::getTenantModelForeignKey(), Filament::getTenant()?->id) : $rule
                                    )
                                    ->required()
                                    ->maxLength(255),

                                // Forms\Components\TextInput::make('guard_name')
                                //     ->label(__('filament-shield::filament-shield.field.guard_name'))
                                //     ->default(Utils::getFilamentAuthGuard())
                                //     ->nullable()
                                //     ->maxLength(255),
                                Forms\Components\Select::make('guard_name')
                                    ->label(__('filament-shield::filament-shield.field.guard_name') . '-xx')
                                    ->options(
                                        collect(array_keys(config('auth.guards')))
                                            ->mapWithKeys(fn($guard) => [$guard => Str::headline($guard)])
                                            ->toArray()
                                    )
                                    ->default(Utils::getFilamentAuthGuard())
                                    ->nullable(),

                                Forms\Components\Select::make(config('permission.column_names.team_foreign_key'))
                                    ->label(__('filament-shield::filament-shield.field.team'))
                                    ->placeholder(__('filament-shield::filament-shield.field.team.placeholder'))
                                    /** @phpstan-ignore-next-line */
                                    ->default([Filament::getTenant()?->id])
                                    ->options(fn(): Arrayable => Utils::getTenantModel() ? Utils::getTenantModel()::pluck('name', 'id') : collect())
                                    ->hidden(fn(): bool => ! (static::shield()->isCentralApp() && Utils::isTenancyEnabled()))
                                    ->dehydrated(fn(): bool => ! (static::shield()->isCentralApp() && Utils::isTenancyEnabled())),
                                ShieldSelectAllToggle::make('select_all')
                                    ->onIcon('heroicon-s-shield-check')
                                    ->offIcon('heroicon-s-shield-exclamation')
                                    ->label(__('filament-shield::filament-shield.field.select_all.name'))
                                    ->helperText(fn(): HtmlString => new HtmlString(__('filament-shield::filament-shield.field.select_all.message')))
                                    ->dehydrated(fn(bool $state): bool => $state),

                            ])
                            ->columns([
                                'sm' => 2,
                                'lg' => 3,
                            ]),
                    ]),
                static::getShieldFormComponents(),
            ]);
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }
}
