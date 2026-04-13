<?php

namespace App\Http\Requests\Post;

use App\Models\Post\PostCommentPreset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePostCommentRequest extends FormRequest
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
            'comment_preset_id' => ['nullable', 'integer', 'exists:post_comment_presets,id'],
            'body' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $presetId = $this->input('comment_preset_id');
            $body = $this->input('body');
            $bodyTrimmed = is_string($body) ? trim($body) : '';

            if ($presetId === null && $bodyTrimmed === '') {
                $validator->errors()->add('body', 'Either a preset comment or custom text is required.');
            }

            if ($presetId !== null) {
                $preset = PostCommentPreset::query()->whereKey($presetId)->first();
                if ($preset && ! $preset->is_active) {
                    $validator->errors()->add('comment_preset_id', 'This comment option is not available.');
                }
            }
        });
    }
}
