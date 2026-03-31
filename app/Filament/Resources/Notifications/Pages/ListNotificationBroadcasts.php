<?php

namespace App\Filament\Resources\Notifications\Pages;

use App\Enums\Notifications\NotificationTopic;
use App\Filament\Resources\Notifications\NotificationBroadcastResource;
use App\Services\Notifications\BroadcastNotificationService;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;

class ListNotificationBroadcasts extends ListRecords
{
    protected static string $resource = NotificationBroadcastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('filament.notifications.send_notification'))
                ->visible(fn (): bool => auth()->guard('admin')->user()?->can('send_notification_broadcasts') ?? false)
                ->createAnother(false)
                ->using(function (array $data): Model {
                    $service = app(BroadcastNotificationService::class);

                    $topic = NotificationTopic::from($data['topic']);

                    $payload = [
                        'title' => (string) ($data['title'] ?? ''),
                        'body' => $data['body'] ?? null,
                        'image' => $data['image'] ?? null,
                        'target_type' => $data['target_type'] ?? null,
                        'target_id' => isset($data['target_id']) ? (int) $data['target_id'] : null,
                    ];

                    /** @var \App\Models\User\Admin|null $admin */
                    $admin = auth()->guard('admin')->user();

                    $broadcast = $service->broadcast($topic, $payload, $admin);

                    FilamentNotification::make()
                        ->title(__('filament.notifications.sent'))
                        ->body(__('filament.notifications.created_count', ['count' => 1]))
                        ->success()
                        ->send();

                    return $broadcast;
                }),
        ];
    }
}
