<?php

namespace App\Filament\Resources\Notifications\Pages;

use App\Filament\Resources\Notifications\NotificationResource;
use App\Models\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewNotification extends ViewRecord
{
    protected static string $resource = NotificationResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $notification = $this->getRecord();

        if ($notification instanceof Notification && blank($notification->read_at)) {
            $notification->forceFill(['read_at' => now()])->save();
        }
    }
}
