<?php

namespace App\Filament\Resources\Notifications\Pages;

use App\Filament\Resources\Notifications\NotificationResource;
use App\Models\User\Admin;
use App\Models\User\User;
use App\Services\Notifications\NotificationService;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Resources\Pages\CreateRecord;

class CreateNotification extends CreateRecord
{
    protected static string $resource = NotificationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // We don't persist the form record directly via Filament create.
        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $service = app(NotificationService::class);

        $recipientType = $data['recipient_type'] ?? 'user';
        $recipientIds = $data['recipient_ids'] ?? [];

        $payload = [
            'title' => (string) ($data['title'] ?? ''),
            'body' => $data['body'] ?? null,
            'image' => $data['image'] ?? null,
            'target_type' => $data['target_type'] ?? null,
            'target_id' => isset($data['target_id']) ? (int) $data['target_id'] : null,
            'data' => $this->decodeJson($data['data_json'] ?? null),
        ];

        $notifiables = match ($recipientType) {
            'admin' => Admin::query()->whereIn('id', $recipientIds)->get(),
            default => User::query()->whereIn('id', $recipientIds)->get(),
        };

        $ids = $service->notifyMany($notifiables, $payload);

        FilamentNotification::make()
            ->title(__('Sent'))
            ->body(__('Notifications created: :count', ['count' => count($ids)]))
            ->success()
            ->send();

        // Return last created notification to satisfy Filament flow.
        /** @var int $lastId */
        $lastId = (int) (end($ids) ?: 0);

        return \App\Models\Notifications\Notification::query()->findOrFail($lastId);
    }

    protected function decodeJson(?string $json): ?array
    {
        if (! $json) {
            return null;
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : null;
    }
}
