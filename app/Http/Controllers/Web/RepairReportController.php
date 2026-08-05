<?php

namespace App\Http\Controllers\Web;

use App\Enums\DamageReportStatus;
use App\Enums\RepairStatus;
use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Models\RepairReport;
use App\Models\Vendor;
use App\Services\PublicReportMedia;
use App\Services\ReportNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RepairReportController extends Controller
{
    public function create(DamageReport $damageReport): View|RedirectResponse
    {
        $this->ensureCanSubmit($damageReport);

        $repair = $damageReport->repairReport;

        if ($repair && $repair->status !== RepairStatus::Revisi->value) {
            return redirect()
                ->route('laporan.status', ['nomor' => $damageReport->nomor_laporan])
                ->with('info', 'Laporan hasil perbaikan untuk kerusakan ini sudah dikirim.');
        }

        return view('laporan.perbaikan', [
            'damage' => $damageReport->load(['asset.room.building', 'asset.department', 'repairReport.attachments']),
            'vendors' => Vendor::orderBy('nama_vendor')->get(),
        ]);
    }

    public function store(Request $request, DamageReport $damageReport, ReportNumberService $numbers): RedirectResponse
    {
        $this->ensureCanSubmit($damageReport);
        $this->normalizeMoneyInputs($request);

        $data = $request->validate([
            'vendor_id' => ['nullable', 'exists:vendors,id'],
            'jenis_pekerjaan' => ['required', 'string', 'max:255'],
            'uraian_pekerjaan' => ['required', 'string', 'max:2000'],
            'tanggal_pelaksanaan' => ['nullable', 'date'],
            'biaya' => ['nullable', 'numeric', 'min:0'],
            'biaya_jasa' => ['nullable', 'numeric', 'min:0'],
            'lampiran' => ['required', 'array', 'min:1', 'max:10'],
            'lampiran.*.file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'lampiran.*.caption' => ['required', 'string', 'max:255'],
        ]);

        $storedPaths = [];

        try {
            $repair = DB::transaction(function () use ($data, $damageReport, $numbers, $request, &$storedPaths): RepairReport {
                $repair = $damageReport->repairReport;

                if ($repair && $repair->status !== RepairStatus::Revisi->value) {
                    throw ValidationException::withMessages([
                        'repair' => 'Laporan perbaikan untuk laporan kerusakan ini sudah dikirim.',
                    ]);
                }

                $repair ??= new RepairReport([
                    'nomor_laporan' => $numbers->generate('repair'),
                    'damage_report_id' => $damageReport->id,
                    'asset_id' => $damageReport->asset_id,
                    'pelapor_user_id' => $request->user()->id,
                    'teknisi_user_id' => $request->user()->id,
                ]);

                $repair->fill([
                    'vendor_id' => $data['vendor_id'] ?? null,
                    'jenis_pekerjaan' => $data['jenis_pekerjaan'],
                    'uraian_pekerjaan' => $data['uraian_pekerjaan'],
                    'tanggal_pelaksanaan' => $data['tanggal_pelaksanaan'] ?? now()->toDateString(),
                    'biaya' => $data['biaya'] ?? 0,
                    'biaya_jasa' => $data['biaya_jasa'] ?? 0,
                    'status' => RepairStatus::Diajukan->value,
                    'verified_at' => null,
                    'verifikator_user_id' => null,
                    'catatan' => null,
                ])->save();

                foreach ($repair->attachments as $attachment) {
                    PublicReportMedia::delete($attachment->file_path);
                }

                $repair->attachments()->delete();

                foreach ($data['lampiran'] as $index => $item) {
                    $file = $item['file'];
                    $originalName = $file->getClientOriginalName();
                    $mimeType = $file->getClientMimeType();
                    $fileSize = $file->getSize();
                    $path = PublicReportMedia::store($file, 'perbaikan', $repair->tanggal_pelaksanaan, $repair->asset, $item['caption'], $index);
                    $storedPaths[] = $path;

                    $repair->attachments()->create([
                        'category' => 'repair_evidence',
                        'caption' => $item['caption'],
                        'file_path' => $path,
                        'original_name' => $originalName,
                        'mime_type' => $mimeType,
                        'file_size' => $fileSize,
                        'sort_order' => $index,
                        'uploaded_by_user_id' => $request->user()->id,
                    ]);
                }

                return $repair;
            });
        } catch (\Throwable $exception) {
            PublicReportMedia::delete($storedPaths);

            throw $exception;
        }

        return redirect()
            ->route('laporan.status', ['nomor' => $repair->nomor_laporan])
            ->with('success', 'Laporan hasil perbaikan berhasil dikirim.')
            ->with('nomor', $repair->nomor_laporan);
    }

    private function ensureCanSubmit(DamageReport $damageReport): void
    {
        abort_unless($damageReport->status === DamageReportStatus::Disetujui->value, 403);
        abort_unless($damageReport->pelapor_user_id === auth()->id(), 403);
        abort_unless(auth()->user()->can('input laporan perbaikan'), 403);
    }

    private function normalizeMoneyInputs(Request $request): void
    {
        foreach (['biaya', 'biaya_jasa'] as $field) {
            if ($request->has($field)) {
                $request->merge([$field => preg_replace('/\D/', '', (string) $request->input($field)) ?: 0]);
            }
        }
    }
}
