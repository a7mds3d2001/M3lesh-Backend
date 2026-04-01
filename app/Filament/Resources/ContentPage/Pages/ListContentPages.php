<?php

namespace App\Filament\Resources\ContentPage\Pages;

use App\Filament\Resources\ContentPage\ContentPageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContentPages extends ListRecords
{
    protected static string $resource = ContentPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->slideOver()
                ->createAnother(false),
        ];
    }
}
