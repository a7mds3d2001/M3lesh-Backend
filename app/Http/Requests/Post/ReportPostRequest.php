<?php

namespace App\Http\Requests\Post;

use App\Enums\Post\PostReportReason;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ReportPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'reason' => ['required', new Enum(PostReportReason::class)],
            'details' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
