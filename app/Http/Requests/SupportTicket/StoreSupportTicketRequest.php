<?php

namespace App\Http\Requests\SupportTicket;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupportTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'user_id' => 'nullable|exists:users,id',
            'visitor_name' => 'nullable|string|max:255',
            'visitor_phone' => 'nullable|string|max:100',
            'visitor_email' => 'nullable|email|max:255',
            'message' => 'required|string',
            'priority' => 'sometimes|in:low,normal,high',
            'attachments' => 'nullable|array',
            'attachments.*' => 'string|max:5242880', // 5 MB max per attachment
        ];

        // When creating as visitor (no user_id), require visitor_name and at least one contact
        if (! $this->filled('user_id')) {
            $rules['visitor_name'] = 'required|string|max:255';
            $rules['visitor_phone'] = 'required_without:visitor_email|nullable|string|max:100';
            $rules['visitor_email'] = 'required_without:visitor_phone|nullable|email|max:255';
        }

        return $rules;
    }
}
