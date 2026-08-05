<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('kelola pengguna');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermissionTo('kelola pengguna');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('kelola pengguna');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasPermissionTo('kelola pengguna');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->hasPermissionTo('kelola pengguna');
    }

    public function restore(User $user, User $model): bool
    {
        return $user->hasPermissionTo('kelola pengguna');
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasPermissionTo('kelola pengguna');
    }
}
