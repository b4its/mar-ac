<?php

namespace App\Filament\Resources\DamageReports\Pages;

use App\Filament\Resources\DamageReports\DamageReportResource;
use App\Filament\Resources\Shared\Actions\EksporCsvAction;
use App\Services\LaporanCsv;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListDamageReports extends ListRecords
{
    protected static string $resource = DamageReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            EksporCsvAction::make()
                ->query(fn (): StreamedResponse => LaporanCsv::damage($this->getFilteredTableQuery()->get())),
        ];
    }
}
