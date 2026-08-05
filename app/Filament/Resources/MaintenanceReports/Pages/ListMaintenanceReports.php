<?php

namespace App\Filament\Resources\MaintenanceReports\Pages;

use App\Filament\Resources\MaintenanceReports\MaintenanceReportResource;
use App\Filament\Resources\Shared\Actions\EksporCsvAction;
use App\Services\LaporanCsv;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListMaintenanceReports extends ListRecords
{
    protected static string $resource = MaintenanceReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            EksporCsvAction::make()
                ->query(fn (): StreamedResponse => LaporanCsv::maintenance($this->getFilteredTableQuery()->get())),
        ];
    }
}
