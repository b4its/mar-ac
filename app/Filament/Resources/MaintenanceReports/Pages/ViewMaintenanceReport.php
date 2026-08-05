<?php

namespace App\Filament\Resources\MaintenanceReports\Pages;

use App\Filament\Resources\MaintenanceReports\Actions\ApproveAction;
use App\Filament\Resources\MaintenanceReports\Actions\VerifikasiAction;
use App\Filament\Resources\MaintenanceReports\Infolists\MaintenanceReportInfolist;
use App\Filament\Resources\MaintenanceReports\MaintenanceReportResource;
use App\Filament\Resources\Shared\Actions\CetakPdfAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewMaintenanceReport extends ViewRecord
{
    protected static string $resource = MaintenanceReportResource::class;

    public function infolist(Schema $schema): Schema
    {
        return MaintenanceReportInfolist::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            CetakPdfAction::make()
                ->previewUrl(fn ($record): string => route('laporan.pdf.perawatan', $record)),
            VerifikasiAction::make(),
            ApproveAction::make(),
        ];
    }
}
