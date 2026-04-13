<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Public profile fields for another user (no email, phone, or audit metadata).
 *
 * @mixin \App\Models\User\User
 */
class PublicUserProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => storage_public_url($this->image),
            'avatar_id' => $this->avatar_id,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'gender' => $this->gender?->value,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
