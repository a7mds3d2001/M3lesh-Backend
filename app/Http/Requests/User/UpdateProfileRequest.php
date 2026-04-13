<?php

namespace App\Http\Requests\User;

use App\Enums\User\Gender;
use App\Models\User\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof User;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->user();

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'phone')->ignore($user->id),
            ],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'image' => ['sometimes', 'nullable', 'file', 'max:5120', 'mimes:jpeg,jpg,png,gif,webp'],
            'avatar_id' => [
                'sometimes',
                'nullable',
                Rule::excludeIf(fn () => $this->hasFile('image')),
                'integer',
                Rule::exists('avatars', 'id'),
            ],
            'birth_date' => ['sometimes', 'nullable', 'date', 'before_or_equal:today'],
            'gender' => ['sometimes', 'nullable', Rule::enum(Gender::class)],
        ];
    }
}
