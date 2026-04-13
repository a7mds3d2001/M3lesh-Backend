<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Catalog avatar for profile picker (API).
 *
 * @mixin \App\Models\User\Avatar
 */
class AvatarPresetResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image' => storage_public_url($this->image),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
