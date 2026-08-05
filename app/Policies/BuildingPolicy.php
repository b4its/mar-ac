<?php

namespace App\Policies;

use App\Models\Building;
use App\Models\User;

class BuildingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Building $building): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('kelola master data');
    }

    public function update(User $user, Building $building): bool
    {
        return $user->hasPermissionTo('kelola master data');
    }

    public function delete(User $user, Building $building): bool
    {
        return $user->hasPermissionTo('kelola master data');
    }

    public function restore(User $user, Building $building): bool
    {
        return $user->hasPermissionTo('kelola master data');
    }

    public function forceDelete(User $user, Building $building): bool
    {
        return $user->hasPermissionTo('kelola master data');
    }
}
