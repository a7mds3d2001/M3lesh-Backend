<?php

namespace App\Http\Requests\ContentPage;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContentPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title_ar' => 'sometimes|required|string|max:255',
            'content_ar' => 'sometimes|required|string',
            'title_en' => 'sometimes|required|string|max:255',
            'content_en' => 'sometimes|required|string',
            'is_active' => 'boolean',
        ];
    }
}
