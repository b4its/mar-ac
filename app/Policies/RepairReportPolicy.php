<?php

namespace App\Policies;

use App\Models\RepairReport;
use App\Models\User;

class RepairReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([
            'input laporan perbaikan',
            'input laporan kerusakan',
            'input laporan perawatan',
            'verifikasi laporan',
            'approve laporan',
        ]);
    }

    public function view(User $user, RepairReport $report): bool
    {
        return $this->viewAny($user)
            || $report->teknisi_user_id === $user->id
            || $report->pelapor_user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, RepairReport $report): bool
    {
        return false;
    }

    public function delete(User $user, RepairReport $report): bool
    {
        return false;
    }

    public function restore(User $user, RepairReport $report): bool
    {
        return false;
    }

    public function forceDelete(User $user, RepairReport $report): bool
    {
        return false;
    }
}
