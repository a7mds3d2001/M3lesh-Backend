<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Concerns\ReturnsAuditAdminObject;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transforms a User model for API responses.
 *
 * @mixin \App\Models\User\User
 */
class UserResource extends JsonResource
{
    use ReturnsAuditAdminObject;

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
            'phone' => $this->phone,
            'email' => $this->email,
            'created_by' => $this->auditAdminObject('creator'),
            'updated_by' => $this->auditAdminObject('updater'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
