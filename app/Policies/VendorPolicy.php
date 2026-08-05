<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;

class VendorPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Vendor $vendor): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('kelola master data');
    }

    public function update(User $user, Vendor $vendor): bool
    {
        return $user->hasPermissionTo('kelola master data');
    }

    public function delete(User $user, Vendor $vendor): bool
    {
        return $user->hasPermissionTo('kelola master data');
    }

    public function restore(User $user, Vendor $vendor): bool
    {
        return $user->hasPermissionTo('kelola master data');
    }

    public function forceDelete(User $user, Vendor $vendor): bool
    {
        return $user->hasPermissionTo('kelola master data');
    }
}
