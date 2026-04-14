<?php

namespace App\Http\Controllers\Api\User\Avatar;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\AvatarPresetResource;
use App\Models\User\Avatar;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AvatarController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $avatars = Avatar::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return AvatarPresetResource::collection($avatars);
    }
}
