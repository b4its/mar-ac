<?php

namespace App\Filament\Resources\DamageReports\Pages;

use App\Filament\Resources\DamageReports\Actions\ApproveAction;
use App\Filament\Resources\DamageReports\DamageReportResource;
use App\Filament\Resources\DamageReports\Infolists\DamageReportInfolist;
use App\Filament\Resources\Shared\Actions\CetakPdfAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewDamageReport extends ViewRecord
{
    protected static string $resource = DamageReportResource::class;

    public function infolist(Schema $schema): Schema
    {
        return DamageReportInfolist::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            CetakPdfAction::make()
                ->previewUrl(fn ($record): string => route('laporan.pdf.kerusakan', $record)),
            ApproveAction::make(),
        ];
    }
}
