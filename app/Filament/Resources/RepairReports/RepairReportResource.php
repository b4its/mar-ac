<?php

namespace App\Filament\Resources\RepairReports;

use App\Filament\Resources\RepairReports\Infolists\RepairReportInfolist;
use App\Filament\Resources\RepairReports\Pages\EditRepairReport;
use App\Filament\Resources\RepairReports\Pages\ListRepairReports;
use App\Filament\Resources\RepairReports\Pages\ViewRepairReport;
use App\Filament\Resources\RepairReports\Schemas\RepairReportForm;
use App\Filament\Resources\RepairReports\Tables\RepairReportsTable;
use App\Models\RepairReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RepairReportResource extends Resource
{
    protected static ?string $model = RepairReport::class;

    protected static ?string $navigationLabel = 'Laporan Perbaikan';

    protected static ?string $modelLabel = 'Laporan Perbaikan';

    protected static ?string $pluralModelLabel = 'Laporan Perbaikan';

    protected static \UnitEnum|string|null $navigationGroup = 'Pelaporan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    public static function form(Schema $schema): Schema
    {
        return RepairReportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RepairReportsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RepairReportInfolist::configure($schema);
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
            'index' => ListRepairReports::route('/'),
            'view' => ViewRepairReport::route('/{record}'),
            'edit' => EditRepairReport::route('/{record}/edit'),
        ];
    }
}
