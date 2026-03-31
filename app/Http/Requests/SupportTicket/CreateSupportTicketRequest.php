<?php

namespace App\Http\Requests\SupportTicket;

use Illuminate\Foundation\Http\FormRequest;

class CreateSupportTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isGuest = ! $this->user('sanctum');
        $attachmentRule = ['nullable', 'file', 'max:5120', 'mimes:jpeg,jpg,png,gif,webp,pdf']; // 5 MB per file

        if ($isGuest) {
            return [
                'visitor_name' => 'required|string|max:255',
                'visitor_phone' => 'required_without:visitor_email|nullable|string|max:100',
                'visitor_email' => 'required_without:visitor_phone|nullable|email|max:255',
                'message' => 'required|string',
                'attachments' => 'nullable|array',
                'attachments.*' => $attachmentRule,
            ];
        }

        return [
            'message' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => $attachmentRule,
        ];
    }
}
