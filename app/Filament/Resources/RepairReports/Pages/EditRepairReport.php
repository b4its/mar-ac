<?php

namespace App\Filament\Resources\RepairReports\Pages;

use App\Filament\Resources\RepairReports\RepairReportResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRepairReport extends EditRecord
{
    protected static string $resource = RepairReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
