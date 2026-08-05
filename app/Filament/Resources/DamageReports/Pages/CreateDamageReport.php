<?php

namespace App\Filament\Resources\DamageReports\Pages;

use App\Enums\DamageReportStatus;
use App\Filament\Resources\DamageReports\DamageReportResource;
use App\Services\ReportNumberService;
use Filament\Resources\Pages\CreateRecord;

class CreateDamageReport extends CreateRecord
{
    protected static string $resource = DamageReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['nomor_laporan'] = app(ReportNumberService::class)->generate('damage');
        $data['status'] = DamageReportStatus::Dilaporkan->value;
        $data['pelapor_user_id'] ??= auth()->id();

        return $data;
    }
}
