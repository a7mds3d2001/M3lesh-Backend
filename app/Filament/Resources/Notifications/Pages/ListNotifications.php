<?php

namespace App\Filament\Resources\Notifications\Pages;

use App\Filament\Resources\Notifications\NotificationResource;
use App\Models\Notifications\Notification;
use App\Models\User\Admin;
use App\Models\User\User;
use App\Services\Notifications\NotificationService;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;

class ListNotifications extends ListRecords
{
    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('filament.notifications.send_notification'))
                ->slideOver()
                ->visible(fn (): bool => auth()->guard('admin')->user()?->can('send_notifications') ?? false)
                ->createAnother(false)
                ->using(function (array $data): Model {
                    $service = app(NotificationService::class);

                    $recipientType = $data['recipient_type'] ?? 'user';
                    $recipientIds = $data['recipient_ids'] ?? [];

                    $payload = [
                        'title' => (string) ($data['title'] ?? ''),
                        'body' => $data['body'] ?? null,
                        'image' => $data['image'] ?? null,
                        'target_type' => $data['target_type'] ?? null,
                        'target_id' => isset($data['target_id']) ? (int) $data['target_id'] : null,
                    ];

                    $notifiables = match ($recipientType) {
                        'admin' => Admin::query()->whereIn('id', $recipientIds)->get(),
                        default => User::query()->whereIn('id', $recipientIds)->get(),
                    };

                    $ids = $service->notifyMany($notifiables, $payload);

                    FilamentNotification::make()
                        ->title(__('filament.notifications.sent'))
                        ->body(__('filament.notifications.created_count', ['count' => count($ids)]))
                        ->success()
                        ->send();

                    /** @var int $lastId */
                    $lastId = (int) (end($ids) ?: 0);

                    return Notification::query()->findOrFail($lastId);
                }),
        ];
    }
}
