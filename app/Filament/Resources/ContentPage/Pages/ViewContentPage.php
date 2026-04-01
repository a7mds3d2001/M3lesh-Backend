<?php

namespace App\Filament\Resources\ContentPage\Pages;

use App\Filament\Resources\ContentPage\ContentPageResource;
use App\Models\ContentPage\ContentPage;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewContentPage extends ViewRecord
{
    protected static string $resource = ContentPageResource::class;

    protected function getHeaderActions(): array
    {
        /** @var ContentPage $record */
        $record = $this->getRecord();
        $resource = static::getResource();

        return [
            EditAction::make()
                ->slideOver()
                ->hidden(fn () => ! $resource::canEdit($record)),
        ];
    }
}
