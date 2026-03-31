<?php

namespace App\Http\Requests\Notifications;

use App\Enums\Notifications\NotificationTopic;
use App\Models\Notifications\NotificationBroadcast;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class SendBroadcastNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('create', NotificationBroadcast::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'topic' => ['required', new Enum(NotificationTopic::class)],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'target_type' => ['nullable', 'string', 'max:50'],
            'target_id' => ['nullable', 'integer'],
            'data_json' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'image', 'max:5120'],
        ];
    }
}
