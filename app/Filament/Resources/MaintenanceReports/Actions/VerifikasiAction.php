<?php

namespace App\Filament\Resources\MaintenanceReports\Actions;

use App\Enums\ReportStatus;
use App\Models\MaintenanceReport;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Support\Icons\Heroicon;

class VerifikasiAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'verifikasi';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Verifikasi')
            ->icon(Heroicon::OutlinedShieldCheck)
            ->color('blue')
            ->modalHeading('Verifikasi Laporan Perawatan')
            ->modalDescription('Periksa kelengkapan dan kebenaran laporan sebelum dilanjutkan ke persetujuan.')
            ->successNotificationTitle('Laporan berhasil diverifikasi')
            ->form([
                Select::make('hasil')
                    ->label('Hasil Verifikasi')
                    ->options([
                        ReportStatus::Diverifikasi->value => 'Setujui verifikasi',
                        ReportStatus::Ditolak->value => 'Tolak laporan',
                    ])
                    ->searchable()
                    ->required(),
                Textarea::make('catatan')
                    ->label('Catatan Verifikasi')
                    ->requiredWith('hasil'),
            ])
            ->action(function (MaintenanceReport $record, array $data): void {
                $status = ReportStatus::from($data['hasil']);

                $record->update([
                    'status' => $status->value,
                    'verifikator_user_id' => auth()->id(),
                    'verified_at' => now(),
                    'catatan' => $data['catatan'] ?? $record->catatan,
                ]);
            })
            ->visible(fn (MaintenanceReport $record): bool => $record->status === ReportStatus::Diajukan->value
                && auth()->user()->can('verifikasi laporan')
                && $record->pelapor_user_id !== auth()->id());
    }
}
