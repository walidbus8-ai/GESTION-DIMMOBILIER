<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Room;

class RoomPolicy
{
    public function view(User $user, Room $room)
    {
        return $user->id === $room->user_id;
    }

    public function update(User $user, Room $room)
    {
        return $user->id === $room->user_id;
    }

    public function delete(User $user, Room $room)
    {
        return $user->id === $room->user_id;
    }
}