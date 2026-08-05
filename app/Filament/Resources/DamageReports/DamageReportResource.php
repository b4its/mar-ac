<?php

namespace App\Filament\Resources\DamageReports;

use App\Filament\Resources\DamageReports\Infolists\DamageReportInfolist;
use App\Filament\Resources\DamageReports\Pages\CreateDamageReport;
use App\Filament\Resources\DamageReports\Pages\EditDamageReport;
use App\Filament\Resources\DamageReports\Pages\ListDamageReports;
use App\Filament\Resources\DamageReports\Pages\ViewDamageReport;
use App\Filament\Resources\DamageReports\Schemas\DamageReportForm;
use App\Filament\Resources\DamageReports\Tables\DamageReportsTable;
use App\Models\DamageReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DamageReportResource extends Resource
{
    protected static ?string $model = DamageReport::class;

    protected static ?string $navigationLabel = 'Laporan Kerusakan';

    protected static ?string $modelLabel = 'Laporan Kerusakan';

    protected static ?string $pluralModelLabel = 'Laporan Kerusakan';

    protected static \UnitEnum|string|null $navigationGroup = 'Pelaporan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    public static function form(Schema $schema): Schema
    {
        return DamageReportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DamageReportsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DamageReportInfolist::configure($schema);
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
            'index' => ListDamageReports::route('/'),
            'create' => CreateDamageReport::route('/create'),
            'view' => ViewDamageReport::route('/{record}'),
            'edit' => EditDamageReport::route('/{record}/edit'),
        ];
    }
}
