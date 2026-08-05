<?php

namespace App\Filament\Resources\MaintenanceReports\Actions;

use App\Enums\ReportStatus;
use App\Models\MaintenanceReport;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Support\Icons\Heroicon;

class ApproveAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'approve';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Approve')
            ->icon(Heroicon::OutlinedCheckBadge)
            ->color('green')
            ->modalHeading('Persetujuan Laporan Perawatan')
            ->modalDescription('Setujui laporan untuk dieksekusi, atau minta revisi bila ada kekurangan.')
            ->successNotificationTitle('Laporan berhasil diproses')
            ->form([
                Select::make('hasil')
                    ->label('Hasil Persetujuan')
                    ->options([
                        ReportStatus::Disetujui->value => 'Setujui',
                        ReportStatus::Revisi->value => 'Minta revisi',
                    ])
                    ->searchable()
                    ->required(),
                Textarea::make('catatan')
                    ->label('Catatan')
                    ->requiredWith('hasil'),
            ])
            ->action(function (MaintenanceReport $record, array $data): void {
                $status = ReportStatus::from($data['hasil']);

                $record->update([
                    'status' => $status->value,
                    'approver_user_id' => auth()->id(),
                    'approved_at' => now(),
                    'catatan' => $data['catatan'] ?? $record->catatan,
                ]);

                if ($status === ReportStatus::Disetujui) {
                    $record->asset?->update([
                        'last_maintenance_date' => $record->tanggal_pelaksanaan ?? now()->toDateString(),
                    ]);
                }
            })
            ->visible(fn (MaintenanceReport $record): bool => $record->status === ReportStatus::Diverifikasi->value
                && auth()->user()->can('approve laporan')
                && $record->pelapor_user_id !== auth()->id()
                && $record->verifikator_user_id !== auth()->id());
    }
}
