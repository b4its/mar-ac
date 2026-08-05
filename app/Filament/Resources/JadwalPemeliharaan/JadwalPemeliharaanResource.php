<?php

namespace App\Filament\Resources\JadwalPemeliharaan;

use App\Filament\Resources\JadwalPemeliharaan\Pages\CreateJadwalPemeliharaan;
use App\Filament\Resources\JadwalPemeliharaan\Pages\EditJadwalPemeliharaan;
use App\Filament\Resources\JadwalPemeliharaan\Pages\ListJadwalPemeliharaan;
use App\Filament\Resources\JadwalPemeliharaan\Pages\ViewJadwalPemeliharaan;
use App\Filament\Resources\JadwalPemeliharaan\Schemas\JadwalPemeliharaanForm;
use App\Filament\Resources\JadwalPemeliharaan\Tables\JadwalPemeliharaanTable;
use App\Models\JadwalPemeliharaan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JadwalPemeliharaanResource extends Resource
{
    protected static ?string $model = JadwalPemeliharaan::class;

    protected static ?string $slug = 'jadwal-pemeliharaans';

    protected static ?string $navigationLabel = 'Jadwal Pemeliharaan';

    protected static ?string $modelLabel = 'Jadwal Pemeliharaan';

    protected static ?string $pluralModelLabel = 'Jadwal Pemeliharaan';

    protected static \UnitEnum|string|null $navigationGroup = 'Operasional';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    public static function form(Schema $schema): Schema
    {
        return JadwalPemeliharaanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JadwalPemeliharaanTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJadwalPemeliharaan::route('/'),
            'create' => CreateJadwalPemeliharaan::route('/create'),
            'edit' => EditJadwalPemeliharaan::route('/{record}/edit'),
            'view' => ViewJadwalPemeliharaan::route('/{record}'),
        ];
    }
}
