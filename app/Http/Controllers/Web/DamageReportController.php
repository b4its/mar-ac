<?php

namespace App\Http\Controllers\Web;

use App\Enums\DamageLevel;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\DamageReport;
use App\Services\PublicReportMedia;
use App\Services\ReportNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DamageReportController extends Controller
{
    public function create(): View
    {
        return view('laporan.kerusakan', [
            'assets' => Asset::with(['room.building', 'department'])->orderBy('nama_alat')->get(),
            'levels' => DamageLevel::cases(),
        ]);
    }

    public function store(Request $request, ReportNumberService $numbers): RedirectResponse
    {
        $data = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'tingkat_kerusakan' => ['required', 'in:ringan,sedang,berat'],
            'jenis_kerusakan' => ['required', 'string', 'max:255'],
            'uraian_kerusakan' => ['nullable', 'string', 'max:2000'],
            'tanggal_laporan' => ['nullable', 'date'],
            'print_fields' => ['nullable', 'array'],
            'print_fields.tanggal_revisi' => ['nullable', 'string', 'max:255'],
            'print_fields.tanggal_berlaku' => ['nullable', 'string', 'max:255'],
            'print_fields.kode_dokumen' => ['nullable', 'string', 'max:255'],
            'print_fields.nomor_laporan' => ['nullable', 'string', 'max:255'],
            'print_fields.nama_alat' => ['nullable', 'string', 'max:255'],
            'print_fields.no_inventaris' => ['nullable', 'string', 'max:255'],
            'print_fields.lokasi_alat' => ['nullable', 'string', 'max:255'],
            'print_fields.nama_ruangan' => ['nullable', 'string', 'max:255'],
            'print_fields.gedung' => ['nullable', 'string', 'max:255'],
            'print_fields.kode_alat' => ['nullable', 'string', 'max:255'],
            'print_fields.jurusan' => ['nullable', 'string', 'max:255'],
            'print_fields.jurusan_unit' => ['nullable', 'string', 'max:255'],
            'print_fields.pelapor_nama' => ['nullable', 'string', 'max:255'],
            'print_fields.teknisi_nama' => ['nullable', 'string', 'max:255'],
            'print_fields.mengetahui_nama' => ['nullable', 'string', 'max:255'],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'photos_captions' => ['nullable', 'array', 'max:10'],
            'photos_captions.*' => ['nullable', 'string', 'max:255'],
        ]);

        $storedPaths = [];

        try {
            $report = DB::transaction(function () use ($data, $numbers, $request, &$storedPaths): DamageReport {
                $report = DamageReport::create([
                    'nomor_laporan' => $numbers->generate('damage'),
                    'asset_id' => $data['asset_id'],
                    'pelapor_user_id' => $request->user()->id,
                    'tingkat_kerusakan' => $data['tingkat_kerusakan'],
                    'jenis_kerusakan' => $data['jenis_kerusakan'],
                    'uraian_kerusakan' => $data['uraian_kerusakan'] ?? null,
                    'tanggal_laporan' => $data['tanggal_laporan'] ?? now()->toDateString(),
                    'print_fields' => $this->filledPrintFields($data['print_fields'] ?? []),
                    'status' => 'dilaporkan',
                ]);

                foreach ($data['photos'] ?? [] as $index => $file) {
                    $originalName = $file->getClientOriginalName();
                    $mimeType = $file->getClientMimeType();
                    $fileSize = $file->getSize();
                    $caption = $data['photos_captions'][$index] ?? null;
                    $caption = filled($caption) ? $caption : 'Foto Kerusakan '.($index + 1);
                    $path = PublicReportMedia::store($file, 'kerusakan', $report->tanggal_laporan, $report->asset, $caption, $index);
                    $storedPaths[] = $path;

                    $report->attachments()->create([
                        'category' => 'damage_evidence',
                        'slot_key' => 'photo_'.($index + 1),
                        'caption' => $caption,
                        'file_path' => $path,
                        'original_name' => $originalName,
                        'mime_type' => $mimeType,
                        'file_size' => $fileSize,
                        'sort_order' => $index,
                        'uploaded_by_user_id' => $request->user()->id,
                    ]);
                }

                return $report;
            });
        } catch (\Throwable $exception) {
            PublicReportMedia::delete($storedPaths);

            throw $exception;
        }

        return redirect()
            ->route('laporan.status', ['nomor' => $report->nomor_laporan])
            ->with('success', 'Laporan kerusakan berhasil dikirim.')
            ->with('nomor', $report->nomor_laporan);
    }

    private function filledPrintFields(array $fields): ?array
    {
        $fields = collect($fields)
            ->map(fn ($value) => is_string($value) ? trim($value) : $value)
            ->filter(fn ($value) => filled($value))
            ->all();

        return $fields === [] ? null : $fields;
    }
}
