<?php

namespace App\Filament\Resources\DamageReports\Pages;

use App\Filament\Resources\DamageReports\DamageReportResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDamageReport extends EditRecord
{
    protected static string $resource = DamageReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
