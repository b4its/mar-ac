<?php

namespace App\Policies;

use App\Models\JadwalPemeliharaan;
use App\Models\User;

class JadwalPemeliharaanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('kelola jadwal') || $user->hasPermissionTo('lihat dashboard');
    }

    public function view(User $user, JadwalPemeliharaan $jadwal): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('kelola jadwal');
    }

    public function update(User $user, JadwalPemeliharaan $jadwal): bool
    {
        return $user->hasPermissionTo('kelola jadwal');
    }

    public function delete(User $user, JadwalPemeliharaan $jadwal): bool
    {
        return $user->hasPermissionTo('kelola jadwal');
    }

    public function restore(User $user, JadwalPemeliharaan $jadwal): bool
    {
        return $user->hasPermissionTo('kelola jadwal');
    }

    public function forceDelete(User $user, JadwalPemeliharaan $jadwal): bool
    {
        return $user->hasPermissionTo('kelola jadwal');
    }
}
