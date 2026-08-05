<?php

namespace App\Filament\Resources\RepairReports\Pages;

use App\Filament\Resources\RepairReports\Actions\VerifikasiRepairAction;
use App\Filament\Resources\RepairReports\Infolists\RepairReportInfolist;
use App\Filament\Resources\RepairReports\RepairReportResource;
use App\Filament\Resources\Shared\Actions\CetakPdfAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewRepairReport extends ViewRecord
{
    protected static string $resource = RepairReportResource::class;

    public function infolist(Schema $schema): Schema
    {
        return RepairReportInfolist::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            CetakPdfAction::make()
                ->previewUrl(fn ($record): string => route('laporan.pdf.perbaikan', $record)),
            VerifikasiRepairAction::make(),
        ];
    }
}
