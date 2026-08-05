<?php

namespace App\Filament\Resources\JadwalPemeliharaan\Pages;

use App\Filament\Resources\JadwalPemeliharaan\Infolists\JadwalPemeliharaanInfolist;
use App\Filament\Resources\JadwalPemeliharaan\JadwalPemeliharaanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewJadwalPemeliharaan extends ViewRecord
{
    protected static string $resource = JadwalPemeliharaanResource::class;

    public function infolist(Schema $schema): Schema
    {
        return JadwalPemeliharaanInfolist::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
