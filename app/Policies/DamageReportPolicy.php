<?php

namespace App\Policies;

use App\Models\DamageReport;
use App\Models\User;

class DamageReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([
            'input laporan kerusakan',
            'verifikasi laporan',
            'approve laporan',
        ]);
    }

    public function view(User $user, DamageReport $report): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('input laporan kerusakan');
    }

    public function update(User $user, DamageReport $report): bool
    {
        return false;
    }

    public function delete(User $user, DamageReport $report): bool
    {
        return false;
    }

    public function restore(User $user, DamageReport $report): bool
    {
        return false;
    }

    public function forceDelete(User $user, DamageReport $report): bool
    {
        return false;
    }
}
