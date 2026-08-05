<?php

namespace App\Filament\Resources\RepairReports\Pages;

use App\Filament\Resources\RepairReports\RepairReportResource;
use App\Filament\Resources\Shared\Actions\EksporCsvAction;
use App\Services\LaporanCsv;
use Filament\Resources\Pages\ListRecords;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListRepairReports extends ListRecords
{
    protected static string $resource = RepairReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EksporCsvAction::make()
                ->query(fn (): StreamedResponse => LaporanCsv::repair($this->getFilteredTableQuery()->get())),
        ];
    }
}
