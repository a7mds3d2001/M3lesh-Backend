<?php

namespace App\Filament\Resources\PostCommentPreset\Pages;

use App\Filament\Resources\PostCommentPreset\PostCommentPresetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPostCommentPresets extends ListRecords
{
    protected static string $resource = PostCommentPresetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->slideOver()
                ->createAnother(false),
        ];
    }
}
