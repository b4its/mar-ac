<?php

namespace App\Filament\Resources\RepairReports\Actions;

use App\Enums\DamageReportStatus;
use App\Enums\RepairStatus;
use App\Models\RepairReport;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class VerifikasiRepairAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'verifikasi-repair';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Verifikasi Perbaikan')
            ->icon(Heroicon::OutlinedCheckBadge)
            ->color('green')
            ->modalHeading('Verifikasi Laporan Hasil Perbaikan')
            ->modalDescription('Setujui bila hasil perbaikan dan lampiran sudah sesuai, atau minta revisi kepada teknisi.')
            ->successNotificationTitle('Laporan perbaikan berhasil diverifikasi')
            ->form([
                Select::make('hasil')
                    ->label('Hasil Verifikasi')
                    ->options([
                        RepairStatus::Disetujui->value => 'Setujui hasil perbaikan',
                        RepairStatus::Revisi->value => 'Minta revisi',
                    ])
                    ->searchable()
                    ->required(),
                Textarea::make('catatan')
                    ->label('Catatan')
                    ->required(),
            ])
            ->action(function (RepairReport $record, array $data): void {
                DB::transaction(function () use ($record, $data): void {
                    $status = RepairStatus::from($data['hasil']);

                    $record->update([
                        'status' => $status->value,
                        'verifikator_user_id' => auth()->id(),
                        'verified_at' => now(),
                        'catatan' => $data['catatan'],
                    ]);

                    if ($status === RepairStatus::Disetujui) {
                        $record->damageReport?->update([
                            'status' => DamageReportStatus::Selesai->value,
                        ]);
                    }
                });
            })
            ->visible(fn (RepairReport $record): bool => in_array($record->status, [
                RepairStatus::Diajukan->value,
                RepairStatus::Revisi->value,
            ], true) && auth()->user()->can('verifikasi laporan'));
    }
}
