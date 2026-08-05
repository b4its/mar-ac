<?php

namespace App\Filament\Resources\JadwalPemeliharaan\Pages;

use App\Filament\Resources\JadwalPemeliharaan\JadwalPemeliharaanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJadwalPemeliharaan extends ListRecords
{
    protected static string $resource = JadwalPemeliharaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
