<?php

namespace App\Filament\Livewire;

use App\Filament\Resources\Notifications\NotificationResource;
use Filament\Actions\Action;
use Filament\Livewire\DatabaseNotifications as FilamentDatabaseNotificationsComponent;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\DatabaseNotification;

class AdminDatabaseNotifications extends FilamentDatabaseNotificationsComponent
{
    public function removeNotification(string $id): void
    {
        if (! ctype_digit($id)) {
            return;
        }

        $this->getNotificationsQuery()
            ->where('id', (int) $id)
            ->delete();
    }

    public function markNotificationAsRead(string $id): void
    {
        if (! ctype_digit($id)) {
            return;
        }

        $this->getNotificationsQuery()
            ->where('id', (int) $id)
            ->update(['read_at' => now()]);
    }

    public function markNotificationAsUnread(string $id): void
    {
        if (! ctype_digit($id)) {
            return;
        }

        $this->getNotificationsQuery()
            ->where('id', (int) $id)
            ->update(['read_at' => null]);
    }

    public function getUser(): Model|Authenticatable|null
    {
        return auth('admin')->user();
    }

    public function clearNotificationsAction(): Action
    {
        return parent::clearNotificationsAction()->hidden();
    }

    public function getNotificationsQuery(): Builder|Relation
    {
        $user = $this->getUser();

        if (! $user) {
            abort(401);
        }

        return $user->notifications();
    }

    public function getNotification(DatabaseNotification $notification): Notification
    {
        return Notification::make((string) $notification->getKey())
            ->title((string) ($notification->getAttribute('title') ?? ''))
            ->body($notification->getAttribute('body'))
            ->duration('persistent')
            ->date($this->formatNotificationDate($notification->getAttributeValue('created_at')))
            ->actions([
                Action::make('view')
                    ->label(__('filament.actions.view'))
                    ->url(NotificationResource::getUrl('view', ['record' => $notification->getKey()])),
            ]);
    }
}
