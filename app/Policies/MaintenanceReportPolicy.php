<?php

namespace App\Policies;

use App\Models\MaintenanceReport;
use App\Models\User;

class MaintenanceReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([
            'input laporan perawatan',
            'verifikasi laporan',
            'approve laporan',
        ]);
    }

    public function view(User $user, MaintenanceReport $report): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('input laporan perawatan');
    }

    public function update(User $user, MaintenanceReport $report): bool
    {
        return false;
    }

    public function delete(User $user, MaintenanceReport $report): bool
    {
        return false;
    }

    public function restore(User $user, MaintenanceReport $report): bool
    {
        return false;
    }

    public function forceDelete(User $user, MaintenanceReport $report): bool
    {
        return false;
    }
}
