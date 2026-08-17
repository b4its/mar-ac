<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\MaintenanceReport;
use App\Models\Vendor;
use App\Services\PublicReportMedia;
use App\Services\ReportNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MaintenanceReportController extends Controller
{
    public function create(): View
    {
        return view('laporan.perawatan', [
            'assets' => Asset::with(['room.building', 'department'])->orderBy('nama_alat')->get(),
            'vendors' => Vendor::orderBy('nama_vendor')->get(),
        ]);
    }

    public function store(Request $request, ReportNumberService $numbers): RedirectResponse
    {
        $this->normalizeMoneyInputs($request);

        $data = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'vendor_id' => ['nullable', 'exists:vendors,id'],
            'jenis_pekerjaan' => ['required', 'string', 'max:255'],
            'uraian_pekerjaan' => ['nullable', 'string', 'max:2000'],
            'tanggal_pelaksanaan' => ['nullable', 'date'],
            'biaya' => ['nullable', 'numeric', 'min:0'],
            'biaya_jasa' => ['nullable', 'numeric', 'min:0'],
            // Bagian kedua (opsional, maksimal dua bagian dalam satu dokumen)
            'asset_id_2' => ['nullable', 'exists:assets,id'],
            'jenis_pekerjaan_2' => ['required_with:asset_id_2', 'string', 'max:255'],
            'uraian_pekerjaan_2' => ['nullable', 'string', 'max:2000'],
            'tanggal_pelaksanaan_2' => ['nullable', 'date'],
            'biaya_2' => ['nullable', 'numeric', 'min:0'],
            'biaya_jasa_2' => ['nullable', 'numeric', 'min:0'],
            'print_fields' => ['nullable', 'array'],
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
            'print_fields.material_suku_cadang' => ['nullable', 'string', 'max:255'],
            'print_fields.kode_material' => ['nullable', 'string', 'max:255'],
            'print_fields.pelaksana_nama' => ['nullable', 'string', 'max:255'],
            'print_fields.pemeriksa_nama' => ['nullable', 'string', 'max:255'],
            'print_fields.mengetahui_nama' => ['nullable', 'string', 'max:255'],
            'foto_indoor' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'foto_outdoor' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'foto_kartu' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'foto_extra' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'caption_extra' => ['required_with:foto_extra', 'nullable', 'string', 'max:255'],
            'foto_indoor_2' => ['required_with:asset_id_2', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'foto_outdoor_2' => ['required_with:asset_id_2', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'foto_kartu_2' => ['required_with:asset_id_2', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'foto_extra_2' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'caption_extra_2' => ['required_with:foto_extra_2', 'nullable', 'string', 'max:255'],
        ]);

        $storedPaths = [];

        try {
            $report = DB::transaction(function () use ($data, $numbers, $request, &$storedPaths): MaintenanceReport {
                $report = MaintenanceReport::create([
                    'nomor_laporan' => $numbers->generate('maintenance'),
                    'asset_id' => $data['asset_id'],
                    'vendor_id' => $data['vendor_id'] ?? null,
                    'pelapor_user_id' => $request->user()->id,
                    'jenis_pekerjaan' => $data['jenis_pekerjaan'],
                    'uraian_pekerjaan' => $data['uraian_pekerjaan'] ?? null,
                    'tanggal_pelaksanaan' => $data['tanggal_pelaksanaan'] ?? now()->toDateString(),
                    'biaya' => $data['biaya'] ?? 0,
                    'biaya_jasa' => $data['biaya_jasa'] ?? 0,
                    'print_fields' => $this->filledPrintFields($data['print_fields'] ?? []),
                    'status' => 'diajukan',
                ]);

                // Bagian pertama (kolom utama laporan)
                $storedPaths = array_merge($storedPaths, $this->storeSectionAttachments(
                    $report,
                    $request,
                    $data,
                    'foto_indoor',
                    'foto_outdoor',
                    'foto_kartu',
                    1,
                    $storedPaths,
                    'foto_extra',
                    'caption_extra',
                ));

                // Bagian kedua (opsional, maksimal dua bagian)
                if (filled($data['asset_id_2'] ?? null)) {
                    $report->items()->create([
                        'bagian' => 2,
                        'asset_id' => $data['asset_id_2'],
                        'jenis_pekerjaan' => $data['jenis_pekerjaan_2'],
                        'uraian_pekerjaan' => $data['uraian_pekerjaan_2'] ?? null,
                        'tanggal_pelaksanaan' => $data['tanggal_pelaksanaan_2'] ?? now()->toDateString(),
                        'biaya' => $data['biaya_2'] ?? 0,
                        'biaya_jasa' => $data['biaya_jasa_2'] ?? 0,
                        'sort_order' => 1,
                    ]);

                    $storedPaths = array_merge($storedPaths, $this->storeSectionAttachments(
                        $report,
                        $request,
                        $data,
                        'foto_indoor_2',
                        'foto_outdoor_2',
                        'foto_kartu_2',
                        2,
                        $storedPaths,
                        'foto_extra_2',
                        'caption_extra_2',
                    ));
                }

                $this->updateLastMaintenanceDate($data);

                return $report;
            });
        } catch (\Throwable $exception) {
            PublicReportMedia::delete($storedPaths);

            throw $exception;
        }

        return redirect()
            ->route('laporan.status', ['nomor' => $report->nomor_laporan])
            ->with('success', 'Kartu pelaporan hasil perawatan berhasil dikirim.')
            ->with('nomor', $report->nomor_laporan);
    }

    /**
     * Menyimpan tiga foto wajib pada satu bagian (section) laporan perawatan,
     * plus satu lampiran tambahan opsional (gambar + caption) per bagian.
     *
     * @param  array<int, string>  $storedPaths
     * @return array<int, string>
     */
    private function storeSectionAttachments(
        MaintenanceReport $report,
        Request $request,
        array $data,
        string $fieldIndoor,
        string $fieldOutdoor,
        string $fieldCard,
        int $bagian,
        array $storedPaths,
        ?string $fieldFotoExtra = null,
        ?string $fieldCaptionExtra = null,
    ): array {
        $slots = [
            $fieldIndoor => ['indoor_cleaning', 'Pencucian AC Indoor'],
            $fieldOutdoor => ['outdoor_cleaning', 'Pencucian AC Outdoor'],
            $fieldCard => ['maintenance_card', 'Kartu Perawatan'],
        ];

        foreach ($slots as $field => [$baseSlotKey, $baseCaption]) {
            $file = $data[$field];
            $slotKey = $bagian === 1 ? $baseSlotKey : $baseSlotKey.'_2';
            $caption = $bagian === 1 ? $baseCaption : $baseCaption.' (Bagian 2)';
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getClientMimeType();
            $fileSize = $file->getSize();
            $path = PublicReportMedia::store($file, 'perawatan', $report->tanggal_pelaksanaan, $report->asset, $caption, count($storedPaths));
            $storedPaths[] = $path;

            $report->attachments()->create([
                'category' => 'maintenance_evidence',
                'slot_key' => $slotKey,
                'caption' => $caption,
                'file_path' => $path,
                'original_name' => $originalName,
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
                'sort_order' => count($storedPaths) - 1,
                'uploaded_by_user_id' => $request->user()->id,
            ]);
        }

        if ($fieldFotoExtra && $fieldCaptionExtra && filled($data[$fieldFotoExtra] ?? null)) {
            $file = $data[$fieldFotoExtra];
            $slotKey = $bagian === 1 ? 'lampiran_tambahan' : 'lampiran_tambahan_2';
            $caption = trim((string) ($data[$fieldCaptionExtra] ?? ''));
            $path = PublicReportMedia::store($file, 'perawatan', $report->tanggal_pelaksanaan, $report->asset, $caption ?: 'Lampiran Tambahan', count($storedPaths));
            $storedPaths[] = $path;

            $report->attachments()->create([
                'category' => 'maintenance_evidence',
                'slot_key' => $slotKey,
                'caption' => $caption,
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'sort_order' => count($storedPaths) - 1,
                'uploaded_by_user_id' => $request->user()->id,
            ]);
        }

        return $storedPaths;
    }

    /**
     * Mencatat tanggal perawatan terakhir pada aset yang dilaporkan.
     * Hanya maju ke tanggal yang lebih baru, tidak pernah mundur.
     */
    private function updateLastMaintenanceDate(array $data): void
    {
        $sections = [
            ['asset_id' => $data['asset_id'], 'tanggal' => $data['tanggal_pelaksanaan'] ?? now()->toDateString()],
            ['asset_id' => $data['asset_id_2'] ?? null, 'tanggal' => $data['tanggal_pelaksanaan_2'] ?? $data['tanggal_pelaksanaan'] ?? now()->toDateString()],
        ];

        foreach ($sections as $section) {
            if (empty($section['asset_id'])) {
                continue;
            }

            $asset = Asset::find($section['asset_id']);

            if (! $asset) {
                continue;
            }

            $tanggal = Carbon::parse($section['tanggal'])->startOfDay();
            $terakhir = $asset->last_maintenance_date;

            if ($terakhir === null || $terakhir->lt($tanggal)) {
                $asset->update(['last_maintenance_date' => $tanggal->toDateString()]);
            }
        }
    }

    private function filledPrintFields(array $fields): ?array
    {
        $fields = collect($fields)
            ->map(fn ($value) => is_string($value) ? trim($value) : $value)
            ->filter(fn ($value) => filled($value))
            ->all();

        return $fields === [] ? null : $fields;
    }

    private function normalizeMoneyInputs(Request $request): void
    {
        foreach (['biaya', 'biaya_jasa', 'biaya_2', 'biaya_jasa_2'] as $field) {
            if ($request->has($field)) {
                $request->merge([$field => preg_replace('/\D/', '', (string) $request->input($field)) ?: 0]);
            }
        }
    }
}
