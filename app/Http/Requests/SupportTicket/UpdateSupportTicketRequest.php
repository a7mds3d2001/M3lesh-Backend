<?php

namespace App\Http\Requests\SupportTicket;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupportTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => 'sometimes|string',
            'priority' => 'sometimes|in:low,normal,high',
            'status' => 'sometimes|in:open,in_progress,closed',
            'attachments' => 'nullable|array',
            'attachments.*' => 'string|max:5242880', // 5 MB max per attachment
        ];
    }
}
