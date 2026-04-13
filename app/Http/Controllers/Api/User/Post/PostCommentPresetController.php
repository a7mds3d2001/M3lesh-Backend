<?php

namespace App\Http\Controllers\Api\User\Post;

use App\Http\Controllers\Controller;
use App\Http\Resources\Post\PostCommentPresetResource;
use App\Models\Post\PostCommentPreset;
use Illuminate\Http\JsonResponse;

class PostCommentPresetController extends Controller
{
    public function index(): JsonResponse
    {
        $presets = PostCommentPreset::query()
            ->active()
            ->ordered()
            ->get();

        return response()->json(PostCommentPresetResource::collection($presets)->resolve());
    }
}
