<?php

namespace App\Filament\Resources\DamageReports\Actions;

use App\Enums\DamageReportStatus;
use App\Models\DamageReport;
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
            ->label('Proses Persetujuan')
            ->icon(Heroicon::OutlinedCheckBadge)
            ->color('green')
            ->modalHeading('Persetujuan Laporan Kerusakan')
            ->modalDescription('Setujui laporan kerusakan untuk ditindaklanjuti, atau tolak bila tidak valid.')
            ->successNotificationTitle('Laporan berhasil diproses')
            ->form([
                Select::make('hasil')
                    ->label('Hasil Persetujuan')
                    ->options([
                        DamageReportStatus::Disetujui->value => 'Setujui',
                        DamageReportStatus::Ditolak->value => 'Tolak laporan',
                    ])
                    ->searchable()
                    ->required(),
                Textarea::make('catatan')
                    ->label('Catatan')
                    ->requiredWith('hasil'),
            ])
            ->action(function (DamageReport $record, array $data): void {
                $status = DamageReportStatus::from($data['hasil']);

                $record->update([
                    'status' => $status->value,
                    'approved_at' => $status === DamageReportStatus::Disetujui ? now() : null,
                    'approved_by_user_id' => $status === DamageReportStatus::Disetujui ? auth()->id() : null,
                    'catatan' => $data['catatan'] ?? $record->catatan,
                ]);
            })
            ->visible(fn (DamageReport $record): bool => $record->status === DamageReportStatus::Dilaporkan->value
                && auth()->user()->can('approve laporan')
                && $record->pelapor_user_id !== auth()->id());
    }
}
