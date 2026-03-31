<?php

namespace App\Filament\Resources\Admin\Tables;

use App\Models\User\Permission;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PermissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_name')
                    ->label(__('filament.fields.name'))
                    ->searchable(query: function ($query, string $search): \Illuminate\Database\Eloquent\Builder {
                        $term = '%'.$search.'%';

                        return $query->where(function ($q) use ($term) {
                            $q->where('key', 'like', $term)
                                ->orWhere('name_ar', 'like', $term)
                                ->orWhere('name_en', 'like', $term);
                        });
                    })
                    ->weight('bold')
                    ->formatStateUsing(fn ($state, Permission $record): string => $record->display_name),

                TextColumn::make('name')
                    ->label(__('filament.permission.key'))
                    ->searchable(query: fn ($query, string $search) => $query->where('key', 'like', '%'.$search.'%'))
                    ->formatStateUsing(fn (string $state): string => $state),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton(),
            ])
            ->filters([])
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc');
    }
}
