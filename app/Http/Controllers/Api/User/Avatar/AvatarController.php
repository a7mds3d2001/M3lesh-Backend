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
            ->latest('id')
            ->get();

        return AvatarPresetResource::collection($avatars);
    }
}
