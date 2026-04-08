<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization is handled in the controller via Policy.
        return true;
    }

    public function rules(): array
    {
        return [
            'name_en' => 'sometimes|string|max:255',
            'name_ar' => 'sometimes|required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => [
                Rule::exists('permissions', 'id')->where('guard_name', 'admin'),
            ],
        ];
    }
}
