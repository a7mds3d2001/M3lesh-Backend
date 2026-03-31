<?php

namespace App\Http\Requests\SupportTicket;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreSupportTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'string|max:5242880', // 5 MB max per attachment
        ];
    }
}
