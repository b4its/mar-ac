<?php

namespace App\Filament\Resources\RepairReports\Pages;

use App\Enums\RepairStatus;
use App\Filament\Resources\RepairReports\RepairReportResource;
use App\Services\ReportNumberService;
use Filament\Resources\Pages\CreateRecord;

class CreateRepairReport extends CreateRecord
{
    protected static string $resource = RepairReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['nomor_laporan'] = app(ReportNumberService::class)->generate('repair');
        $data['status'] = RepairStatus::Direncanakan->value;
        $data['pelapor_user_id'] ??= auth()->id();

        return $data;
    }
}
