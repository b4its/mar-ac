<?php

namespace App\Filament\Resources\JadwalPemeliharaan\Pages;

use App\Filament\Resources\JadwalPemeliharaan\JadwalPemeliharaanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJadwalPemeliharaan extends EditRecord
{
    protected static string $resource = JadwalPemeliharaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
