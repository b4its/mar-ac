<?php

namespace App\Filament\Resources\MaintenanceReports\Pages;

use App\Filament\Resources\MaintenanceReports\MaintenanceReportResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMaintenanceReport extends EditRecord
{
    protected static string $resource = MaintenanceReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
