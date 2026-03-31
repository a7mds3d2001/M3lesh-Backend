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
                ->hidden(fn () => ! $resource::canEdit($record))
                ->extraAttributes([
                    'id' => 'content-page-page-edit-btn',
                    'style' => 'position:absolute!important;width:1px!important;height:1px!important;margin:-1px!important;padding:0!important;overflow:hidden!important;clip:rect(0,0,0,0)!important;border:0!important',
                ]),
        ];
    }
}
