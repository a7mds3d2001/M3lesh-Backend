<?php

namespace App\Http\Requests\ContentPage;

use Illuminate\Foundation\Http\FormRequest;

class StoreContentPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title_ar' => 'required|string|max:255',
            'content_ar' => 'required|string',
            'title_en' => 'required|string|max:255',
            'content_en' => 'required|string',
            'is_active' => 'boolean',
        ];
    }
}
