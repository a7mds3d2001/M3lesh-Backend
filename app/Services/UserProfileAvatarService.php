<?php

namespace App\Services;

use App\Models\User\Avatar;
use App\Models\User\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class UserProfileAvatarService
{
    /**
     * Copy catalog avatar file into this user's storage folder and return the new path.
     *
     * @throws RuntimeException When the source file is missing or copy fails.
     */
    public function copyCatalogAvatarToUserStorage(User $user, Avatar $avatar): string
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($avatar->image)) {
            throw new RuntimeException('Avatar source file is missing from storage.');
        }

        $ext = pathinfo($avatar->image, PATHINFO_EXTENSION) ?: 'png';
        $dest = 'users/'.$user->id.'/'.Str::ulid().'.'.$ext;

        if (! $disk->copy($avatar->image, $dest)) {
            throw new RuntimeException('Could not copy avatar file to user storage.');
        }

        return $dest;
    }
}
