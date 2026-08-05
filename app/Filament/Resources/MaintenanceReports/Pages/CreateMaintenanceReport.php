<?php

namespace App\Filament\Resources\MaintenanceReports\Pages;

use App\Enums\ReportStatus;
use App\Filament\Resources\MaintenanceReports\MaintenanceReportResource;
use App\Services\ReportNumberService;
use Filament\Resources\Pages\CreateRecord;

class CreateMaintenanceReport extends CreateRecord
{
    protected static string $resource = MaintenanceReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['nomor_laporan'] = app(ReportNumberService::class)->generate('maintenance');
        $data['status'] = ReportStatus::Diajukan->value;
        $data['pelapor_user_id'] ??= auth()->id();

        return $data;
    }
}
