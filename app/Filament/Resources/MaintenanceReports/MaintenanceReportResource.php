<?php

namespace App\Filament\Resources\MaintenanceReports;

use App\Filament\Resources\MaintenanceReports\Infolists\MaintenanceReportInfolist;
use App\Filament\Resources\MaintenanceReports\Pages\CreateMaintenanceReport;
use App\Filament\Resources\MaintenanceReports\Pages\EditMaintenanceReport;
use App\Filament\Resources\MaintenanceReports\Pages\ListMaintenanceReports;
use App\Filament\Resources\MaintenanceReports\Pages\ViewMaintenanceReport;
use App\Filament\Resources\MaintenanceReports\Schemas\MaintenanceReportForm;
use App\Filament\Resources\MaintenanceReports\Tables\MaintenanceReportsTable;
use App\Models\MaintenanceReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MaintenanceReportResource extends Resource
{
    protected static ?string $model = MaintenanceReport::class;

    protected static ?string $navigationLabel = 'Laporan Perawatan';

    protected static ?string $modelLabel = 'Laporan Perawatan';

    protected static ?string $pluralModelLabel = 'Laporan Perawatan';

    protected static \UnitEnum|string|null $navigationGroup = 'Pelaporan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    public static function form(Schema $schema): Schema
    {
        return MaintenanceReportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaintenanceReportsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MaintenanceReportInfolist::configure($schema);
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
            'index' => ListMaintenanceReports::route('/'),
            'create' => CreateMaintenanceReport::route('/create'),
            'view' => ViewMaintenanceReport::route('/{record}'),
            'edit' => EditMaintenanceReport::route('/{record}/edit'),
        ];
    }
}
