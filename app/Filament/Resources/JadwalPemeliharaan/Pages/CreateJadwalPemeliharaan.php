<?php

namespace App\Filament\Resources\JadwalPemeliharaan\Pages;

use App\Filament\Resources\JadwalPemeliharaan\JadwalPemeliharaanResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateJadwalPemeliharaan extends CreateRecord
{
    protected static string $resource = JadwalPemeliharaanResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['created_by_user_id'] = auth()->id();

        return static::getModel()::create($data);
    }
}
