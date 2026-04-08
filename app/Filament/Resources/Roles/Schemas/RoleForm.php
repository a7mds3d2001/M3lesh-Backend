<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles\Schemas;

use App\Filament\Resources\Roles\RoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;
use Livewire\Component as Livewire;

class RoleForm
{
    use HasShieldFormComponents;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name_en')
                                    ->label(__('filament.role.name_en'))
                                    ->required()
                                    ->unique(
                                        ignoreRecord: true,
                                        modifyRuleUsing: fn (Unique $rule): Unique => (function () use ($rule): Unique {
                                            $rule = $rule->where('guard_name', Utils::getFilamentAuthGuard());

                                            if (Utils::isTenancyEnabled()) {
                                                $rule = $rule->where(Utils::getTenantModelForeignKey(), Filament::getTenant()?->getKey());
                                            }

                                            return $rule;
                                        })(),
                                    )
                                    ->maxLength(255),
                                TextInput::make('name_ar')
                                    ->label(__('filament.role.name_ar'))
                                    ->required()
                                    ->maxLength(255),
                                Hidden::make('guard_name')
                                    ->default(Utils::getFilamentAuthGuard())
                                    ->required(),
                                Select::make(config('permission.column_names.team_foreign_key'))
                                    ->label(__('filament-shield::filament-shield.field.team'))
                                    ->placeholder(__('filament-shield::filament-shield.field.team.placeholder'))
                                    ->default(Filament::getTenant()?->getKey())
                                    ->options(fn (): array => in_array(Utils::getTenantModel(), [null, '', '0'], true) ? [] : Utils::getTenantModel()::pluck('name', 'id')->toArray())
                                    ->visible(fn (): bool => RoleResource::shield()->isCentralApp() && Utils::isTenancyEnabled())
                                    ->dehydrated(fn (): bool => RoleResource::shield()->isCentralApp() && Utils::isTenancyEnabled()),
                                Toggle::make('select_all')
                                    ->onIcon('heroicon-s-shield-check')
                                    ->offIcon('heroicon-s-shield-exclamation')
                                    ->label(__('filament-shield::filament-shield.field.select_all.name'))
                                    ->helperText(__('filament-shield::filament-shield.field.select_all.message'))
                                    ->live()
                                    ->afterStateUpdated(function (Livewire $livewire, Set $set, bool $state): void {
                                        static::syncShieldCheckboxListsFromSelectAll($livewire, $set, $state);
                                    })
                                    ->dehydrated(false),
                            ])
                            ->columns([
                                'sm' => 2,
                                'lg' => 3,
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                static::getShieldFormComponents(),
            ]);
    }

    protected static function syncShieldCheckboxListsFromSelectAll(Livewire $livewire, Set $set, bool $state): void
    {
        collect($livewire->form->getFlatComponents())
            ->filter(fn ($component): bool => $component instanceof CheckboxList)
            ->each(function (CheckboxList $component) use ($set, $state): void {
                $set(
                    $component->getName(),
                    $state ? array_keys($component->getOptions()) : [],
                );
            });
    }
}
