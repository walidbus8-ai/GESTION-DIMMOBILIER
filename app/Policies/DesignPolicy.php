<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Design;

class DesignPolicy
{
    public function view(User $user, Design $design)
    {
        return $user->id === $design->room->user_id;
    }

    public function update(User $user, Design $design)
    {
        return $user->id === $design->room->user_id;
    }

    public function delete(User $user, Design $design)
    {
        return $user->id === $design->room->user_id;
    }
}