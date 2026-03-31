<?php

namespace App\Filament\Support;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;

/**
 * Reusable Filament infolist section showing who created/updated the record and when.
 */
class AuditInfolistSection
{
    public static function make(): Section
    {
        return Section::make(__('filament.audit.section_title'))
            ->columnSpanFull()
            ->schema([
                TextEntry::make('creator.name')
                    ->label(__('filament.fields.created_by'))
                    ->placeholder('—')
                    ->icon('heroicon-o-user-plus'),
                TextEntry::make('created_at')
                    ->label(__('filament.fields.created_at'))
                    ->dateTime()
                    ->icon('heroicon-o-calendar')
                    ->placeholder('—'),
                TextEntry::make('updater.name')
                    ->label(__('filament.fields.updated_by'))
                    ->placeholder('—')
                    ->icon('heroicon-o-pencil-square'),
                TextEntry::make('updated_at')
                    ->label(__('filament.fields.updated_at'))
                    ->dateTime()
                    ->icon('heroicon-o-clock')
                    ->since()
                    ->placeholder('—'),
            ])
            ->columns(2);
    }
}
