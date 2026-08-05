<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class RoomPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Room $room): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('kelola master data');
    }

    public function update(User $user, Room $room): bool
    {
        return $user->hasPermissionTo('kelola master data');
    }

    public function delete(User $user, Room $room): bool
    {
        return $user->hasPermissionTo('kelola master data');
    }

    public function restore(User $user, Room $room): bool
    {
        return $user->hasPermissionTo('kelola master data');
    }

    public function forceDelete(User $user, Room $room): bool
    {
        return $user->hasPermissionTo('kelola master data');
    }
}
