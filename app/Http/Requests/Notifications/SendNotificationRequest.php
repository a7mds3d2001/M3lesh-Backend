<?php

namespace App\Http\Requests\Notifications;

use App\Models\Notifications\Notification;
use Illuminate\Foundation\Http\FormRequest;

class SendNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('send', Notification::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'recipient_type' => ['required', 'in:user,admin'],
            'recipient_ids' => ['nullable', 'array'],
            'recipient_ids.*' => ['integer'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'target_type' => ['nullable', 'string', 'max:50'],
            'target_id' => ['nullable', 'integer'],
            'data_json' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'image', 'max:5120'],
        ];
    }
}
