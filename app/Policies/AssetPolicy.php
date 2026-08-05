<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;

class AssetPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Asset $asset): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('kelola master data');
    }

    public function update(User $user, Asset $asset): bool
    {
        return $user->hasPermissionTo('kelola master data');
    }

    public function delete(User $user, Asset $asset): bool
    {
        return $user->hasPermissionTo('kelola master data');
    }

    public function restore(User $user, Asset $asset): bool
    {
        return $user->hasPermissionTo('kelola master data');
    }

    public function forceDelete(User $user, Asset $asset): bool
    {
        return $user->hasPermissionTo('kelola master data');
    }
}
