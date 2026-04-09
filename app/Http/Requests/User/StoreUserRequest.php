<?php

namespace App\Http\Requests\User;

use App\Enums\User\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:users,phone',
            'email' => 'required|email|max:255|unique:users,email',
            'image' => 'nullable|string|max:500',
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'password' => ['required', 'string', Password::min(8)],
        ];
    }
}
