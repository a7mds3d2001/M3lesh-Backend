<?php

namespace App\Jobs\Notifications;

use App\Models\Notifications\Notification;
use App\Models\User\Admin;
use App\Models\User\Device;
use App\Models\User\User;
use App\Services\Fcm\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public int $notificationId) {}

    public function handle(FcmService $fcm): void
    {
        /** @var Notification|null $notification */
        $notification = Notification::find($this->notificationId);
        if (! $notification) {
            return;
        }

        $notifiable = $notification->notifiable;
        if (! $notifiable) {
            return;
        }

        $tokens = $this->tokensForNotifiable($notifiable);
        if ($tokens === []) {
            $notification->forceFill(['sent_at' => now()])->save();

            return;
        }

        $result = $fcm->sendToTokens($tokens, [
            'title' => $notification->title,
            'body' => $notification->body ?? '',
            'image' => $notification->image ? url('/storage/'.$notification->image) : null,
        ], [
            'target_type' => $notification->target_type,
            'target_id' => $notification->target_id,
        ]);

        if ($result['invalid_tokens'] !== []) {
            Device::query()
                ->whereIn('device_token', $result['invalid_tokens'])
                ->delete();
        }

        $notification->forceFill(['sent_at' => now()])->save();
    }

    /**
     * @return array<int, string>
     */
    protected function tokensForNotifiable(object $notifiable): array
    {
        if ($notifiable instanceof User) {
            return Device::query()
                ->where('user_id', $notifiable->id)
                ->whereNotNull('device_token')
                ->pluck('device_token')
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        if ($notifiable instanceof Admin) {
            return Device::query()
                ->where('admin_id', $notifiable->id)
                ->whereNotNull('device_token')
                ->pluck('device_token')
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        return [];
    }
}
