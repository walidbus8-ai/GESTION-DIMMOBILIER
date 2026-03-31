<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Photo;

class PhotoPolicy
{
    public function view(User $user, Photo $photo)
    {
        return $user->id === $photo->room->user_id;
    }

    public function delete(User $user, Photo $photo)
    {
        return $user->id === $photo->room->user_id;
    }
}