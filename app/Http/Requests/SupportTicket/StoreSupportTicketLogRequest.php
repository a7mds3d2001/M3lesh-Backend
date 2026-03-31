<?php

namespace App\Http\Requests\SupportTicket;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupportTicketLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => 'nullable|string',
            'log_type' => 'sometimes|in:comment,status_change,priority_change,internal_note',
            'attachments' => 'nullable|array',
            'attachments.*' => ['nullable', 'file', 'max:5120', 'mimes:jpeg,jpg,png,gif,webp,pdf'], // 5 MB per file
        ];
    }
}
