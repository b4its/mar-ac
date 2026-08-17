<?php

namespace Database\Seeders;

use App\Enums\DamageReportStatus;
use App\Enums\JadwalStatus;
use App\Enums\RepairStatus;
use App\Enums\ReportStatus;
use App\Models\Asset;
use App\Models\Building;
use App\Models\DamageReport;
use App\Models\Department;
use App\Models\JadwalPemeliharaan;
use App\Models\MaintenanceReport;
use App\Models\RepairReport;
use App\Models\Room;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $lokasi = $this->seedLokasi();
        $vendors = $this->seedVendors();
        $assets = $this->seedAssets($lokasi);

        $admin = User::firstOrCreate(
            ['email' => 'admin@upapp.test'],
            ['name' => 'Admin UPA.PP', 'password' => Hash::make('password')],
        );
        $admin->assignRole('admin');

        $teknisi = User::firstOrCreate(
            ['email' => 'teknisi@upapp.test'],
            ['name' => 'Teknisi UPA.PP', 'password' => Hash::make('password')],
        );
        $teknisi->assignRole('teknisi');

        $this->demoReports($admin, $teknisi, $assets, $vendors);
        $this->demoJadwal($admin, $assets);
    }

    /**
     * Gedung → ruangan → jurusan yang beragam agar hasil filter lokasi
     * bercabang menampilkan kombinasi yang berbeda-beda.
     */
    private function seedLokasi(): array
    {
        $gedungA = Building::create(['nama_gedung' => 'Gedung A', 'kode_gedung' => 'A']);
        $gedungB = Building::create(['nama_gedung' => 'Gedung B', 'kode_gedung' => 'B']);
        $gedungC = Building::create(['nama_gedung' => 'Gedung C', 'kode_gedung' => 'C']);

        $ruangan = [];
        foreach ([
            [$gedungA, 'Ruang Administrasi', 'A-1'],
            [$gedungA, 'Ruang Server', 'A-2'],
            [$gedungA, 'Ruang Rapat', 'A-3'],
            [$gedungA, 'Ruang Keuangan', 'A-4'],
            [$gedungB, 'Lab Komputer 1', 'B-1'],
            [$gedungB, 'Lab Komputer 2', 'B-2'],
            [$gedungB, 'Lab Elektro', 'B-3'],
            [$gedungB, 'Ruang Guru', 'B-4'],
            [$gedungC, 'Lab Akuntansi', 'C-1'],
            [$gedungC, 'Ruang Kelas 1', 'C-2'],
            [$gedungC, 'Perpustakaan', 'C-3'],
            [$gedungC, 'Aula', 'C-4'],
        ] as [$gedung, $nama, $kode]) {
            $ruangan[$kode] = Room::create(['building_id' => $gedung->id, 'nama_ruangan' => $nama, 'kode_ruangan' => $kode]);
        }

        $jurusan = [];
        foreach ([
            ['Teknik Informatika', 'TI'],
            ['Teknik Arsitektur & Sipil', 'TAS'],
            ['Akuntansi', 'AK'],
            ['Teknik Elektro', 'TE'],
            ['Teknik Mesin', 'TM'],
        ] as [$nama, $kode]) {
            $jurusan[$kode] = Department::create(['nama_jurusan' => $nama, 'kode_jurusan' => $kode]);
        }

        return ['gedung' => compact('gedungA', 'gedungB', 'gedungC'), 'ruangan' => $ruangan, 'jurusan' => $jurusan];
    }

    private function seedVendors(): array
    {
        $vendors = [];
        foreach ([
            ['PT AC Sejahtera', 'Budi Santoso', '0812-3456-7890', 'Jl. Pahlawan No. 12, Samarinda'],
            ['CV Teknik Mandiri', 'Agus Wijaya', '0813-9876-5432', 'Jl. S. Parman No. 45, Samarinda'],
            ['PT Dingin Sejuk Abadi', 'Rina Kartika', '0821-1111-2222', 'Jl. Gatot Subroto No. 88, Samarinda'],
            ['CV Service AC Borneo', 'Dedi Hartono', '0852-3333-4444', 'Jl. AW Syahrani No. 21, Samarinda'],
        ] as [$nama, $kontak, $telepon, $alamat]) {
            $vendors[$nama] = Vendor::create([
                'nama_vendor' => $nama,
                'kontak' => $kontak,
                'telepon' => $telepon,
                'alamat' => $alamat,
            ]);
        }

        return $vendors;
    }

    /**
     * 16 aset AC dengan merk, kapasitas, tahun, dan kondisi yang beragam.
     * Satu aset sengaja belum memiliki ruangan agar tampak berbeda dari yang lain.
     */
    private function seedAssets(array $lokasi): array
    {
        $r = $lokasi['ruangan'];
        $j = $lokasi['jurusan'];

        $assets = [];
        foreach ([
            ['AC-001', 'AC Split 2 PK', 'Daikin', '2 PK', '2022', 'baik', 'INV-2022-0001', $r['A-1'], $j['AK']],
            ['AC-002', 'AC Split 1,5 PK', 'Panasonic', '1,5 PK', '2021', 'baik', 'INV-2021-0002', $r['A-2'], $j['TI']],
            ['AC-003', 'AC Cassette 3 PK', 'Gree', '3 PK', '2023', 'baik', 'INV-2023-0003', $r['A-3'], $j['AK']],
            ['AC-004', 'AC Split 1 PK', 'Sharp', '1 PK', '2019', 'rusak_ringan', 'INV-2019-0004', $r['A-4'], $j['AK']],
            ['AC-005', 'AC Standing 5 PK', 'LG', '5 PK', '2020', 'rusak_sedang', 'INV-2020-0005', $r['A-3'], $j['AK']],
            ['AC-006', 'AC Split 2 PK', 'Daikin', '2 PK', '2023', 'baik', 'INV-2023-0006', $r['B-1'], $j['TI']],
            ['AC-007', 'AC Split 1 PK', 'Samsung', '1 PK', '2022', 'baik', 'INV-2022-0007', $r['B-2'], $j['TI']],
            ['AC-008', 'AC Window 1,5 PK', 'Mitsubishi', '1,5 PK', '2018', 'rusak_berat', 'INV-2018-0008', $r['B-3'], $j['TE']],
            ['AC-009', 'AC Split 2 PK', 'Carrier', '2 PK', '2021', 'rusak_ringan', 'INV-2021-0009', $r['B-4'], $j['TAS']],
            ['AC-010', 'AC Cassette 3 PK', 'Gree', '3 PK', '2024', 'baik', 'INV-2024-0010', $r['B-3'], $j['TE']],
            ['AC-011', 'AC Split 1,5 PK', 'Panasonic', '1,5 PK', '2022', 'baik', 'INV-2022-0011', $r['C-1'], $j['AK']],
            ['AC-012', 'AC Split 1 PK', 'Sharp', '1 PK', '2020', 'baik', 'INV-2020-0012', $r['C-2'], $j['TAS']],
            ['AC-013', 'AC Standing 3 PK', 'LG', '3 PK', '2019', 'rusak_ringan', 'INV-2019-0013', $r['C-3'], $j['TAS']],
            ['AC-014', 'AC Duct 4 PK', 'Daikin', '4 PK', '2024', 'baik', 'INV-2024-0014', $r['C-4'], $j['TM']],
            ['AC-015', 'AC Split 2 PK', 'Samsung', '2 PK', '2021', 'rusak_sedang', 'INV-2021-0015', $r['C-1'], $j['AK']],
            ['AC-016', 'AC Portable 1 PK', 'Midea', '1 PK', '2024', 'baik', 'INV-2024-0016', null, $j['TM']],
        ] as [$kode, $nama, $merk, $kapasitas, $tahun, $status, $inventaris, $ruangan, $jurusan]) {
            $assets[$kode] = Asset::create([
                'nama_alat' => $nama,
                'jenis_alat' => 'Pendingin Ruangan',
                'kode_alat' => $kode,
                'no_inventaris' => $inventaris,
                'room_id' => $ruangan?->id,
                'department_id' => $jurusan?->id,
                'kapasitas' => $kapasitas,
                'merk' => $merk,
                'tahun_pemakaian' => $tahun,
                'status' => $status,
            ]);
        }

        return $assets;
    }

    public function demoJadwal(User $admin, array $assets): void
    {
        if (JadwalPemeliharaan::exists()) {
            return;
        }

        JadwalPemeliharaan::create([
            'asset_id' => $assets['AC-001']->id,
            'tanggal_jadwal' => now()->addDays(7),
            'jenis_pekerjaan' => 'Pencucian AC Indoor & Outdoor',
            'catatan' => 'Jadwal rutin bulanan kerja sama vendor.',
            'status' => JadwalStatus::Terjadwal->value,
            'created_by_user_id' => $admin->id,
        ]);

        JadwalPemeliharaan::create([
            'asset_id' => $assets['AC-014']->id,
            'tanggal_jadwal' => now()->addDays(14),
            'jenis_pekerjaan' => 'Cek tekanan freon & kebersihan filter',
            'catatan' => 'Jadwal rutin dua mingguan.',
            'status' => JadwalStatus::Terjadwal->value,
            'created_by_user_id' => $admin->id,
        ]);

        JadwalPemeliharaan::create([
            'asset_id' => $assets['AC-009']->id,
            'tanggal_jadwal' => now()->subDays(3),
            'jenis_pekerjaan' => 'Penggantian filter udara indoor',
            'catatan' => 'Sudah dilaksanakan oleh teknisi.',
            'status' => JadwalStatus::Selesai->value,
            'created_by_user_id' => $admin->id,
        ]);
    }

    public function demoReports(User $admin, User $teknisi, array $assets, array $vendors): void
    {
        if (DamageReport::exists()) {
            return;
        }

        $year = now()->format('Y');
        $reportNumber = fn (int $no, string $type): string => sprintf('%03d/UPA.PP/%s/%s', $no, $type, $year);

        // --- Laporan kerusakan dengan tingkat & status yang berbeda-beda ---
        $damage1 = DamageReport::create([
            'nomor_laporan' => $reportNumber(1, 'KSR'),
            'asset_id' => $assets['AC-008']->id,
            'pelapor_user_id' => $teknisi->id,
            'tingkat_kerusakan' => 'berat',
            'jenis_kerusakan' => 'Mesin tidak hidup sama sekali',
            'uraian_kerusakan' => 'AC Window tidak merespons saat dinyalakan. Indikasi kerusakan pada motor kompresor.',
            'tanggal_laporan' => now()->subDays(2),
            'status' => DamageReportStatus::Dilaporkan->value,
            'catatan' => 'Menunggu pemeriksaan lebih lanjut oleh teknisi.',
        ]);

        $damage2 = DamageReport::create([
            'nomor_laporan' => $reportNumber(2, 'KSR'),
            'asset_id' => $assets['AC-005']->id,
            'pelapor_user_id' => $teknisi->id,
            'tingkat_kerusakan' => 'sedang',
            'jenis_kerusakan' => 'Kompresor mati',
            'uraian_kerusakan' => 'AC tidak dingin meskipun sudah diisi ulang freon. Diduga kompresor mengalami kerusakan.',
            'tanggal_laporan' => now()->subDays(5),
            'status' => DamageReportStatus::Disetujui->value,
            'approved_at' => now()->subDays(4),
            'approved_by_user_id' => $admin->id,
            'catatan' => 'Disetujui untuk ditindaklanjuti. Percepat penanganan sebelum ujian berlangsung.',
        ]);

        $damage3 = DamageReport::create([
            'nomor_laporan' => $reportNumber(3, 'KSR'),
            'asset_id' => $assets['AC-015']->id,
            'pelapor_user_id' => $teknisi->id,
            'tingkat_kerusakan' => 'ringan',
            'jenis_kerusakan' => 'Bunyi berisik pada bagian outdoor',
            'uraian_kerusakan' => 'Muncul bunyi berisik saat AC dinyalakan, kemungkinan baut fan longgar.',
            'tanggal_laporan' => now()->subDays(10),
            'status' => DamageReportStatus::Ditolak->value,
            'approved_at' => now()->subDays(8),
            'approved_by_user_id' => $admin->id,
            'catatan' => 'Ditolak: biaya perbaikan ditanggung pihak ketiga berdasarkan masa garansi.',
        ]);

        // --- Laporan perbaikan dengan status berbeda ---
        $repair1 = RepairReport::create([
            'nomor_laporan' => $reportNumber(1, 'PRB'),
            'damage_report_id' => $damage2->id,
            'asset_id' => $damage2->asset_id,
            'vendor_id' => $vendors['PT AC Sejahtera']->id,
            'pelapor_user_id' => $teknisi->id,
            'teknisi_user_id' => $teknisi->id,
            'jenis_pekerjaan' => 'Penggantian kompresor AC',
            'uraian_pekerjaan' => 'Kompresor diganti dengan unit baru, freon diisi ulang, dan unit diuji selama 2 jam.',
            'tanggal_pelaksanaan' => now()->subDays(3),
            'tanggal_selesai' => now()->subDays(2),
            'biaya' => 1850000,
            'biaya_jasa' => 450000,
            'status' => RepairStatus::Disetujui->value,
            'verified_at' => now()->subDays(2),
            'catatan' => 'Selesai dikerjakan sesuai jadwal kerja sama vendor.',
        ]);

        $this->attachDemoImages($repair1, [
            ['caption' => 'Kondisi kompresor lama', 'color' => [150, 60, 60]],
            ['caption' => 'Kompresor baru terpasang', 'color' => [60, 130, 90]],
        ], $teknisi);

        $repair2 = RepairReport::create([
            'nomor_laporan' => $reportNumber(2, 'PRB'),
            'damage_report_id' => $damage3->id,
            'asset_id' => $damage3->asset_id,
            'vendor_id' => $vendors['PT Dingin Sejuk Abadi']->id,
            'pelapor_user_id' => $teknisi->id,
            'teknisi_user_id' => $teknisi->id,
            'jenis_pekerjaan' => 'Perbaikan fan blower outdoor',
            'uraian_pekerjaan' => 'Pengencangan baut fan dan pelumasan bearing motor blower.',
            'tanggal_pelaksanaan' => now()->subDays(8),
            'biaya' => 350000,
            'biaya_jasa' => 150000,
            'status' => RepairStatus::Diajukan->value,
            'catatan' => 'Menunggu persetujuan atasan.',
        ]);

        // --- Laporan perawatan dengan status berbeda-beda ---
        $maintenance1 = MaintenanceReport::create([
            'nomor_laporan' => $reportNumber(1, 'PRW'),
            'asset_id' => $assets['AC-002']->id,
            'pelapor_user_id' => $teknisi->id,
            'vendor_id' => $vendors['CV Teknik Mandiri']->id,
            'jenis_pekerjaan' => 'Pencucian AC Indoor & Outdoor',
            'uraian_pekerjaan' => 'Pembersihan filter, evaporator, kondensor, dan pengecekan tekanan freon.',
            'tanggal_pelaksanaan' => now()->subDays(6),
            'biaya' => 150000,
            'biaya_jasa' => 100000,
            'status' => ReportStatus::Diajukan->value,
        ]);

        $this->attachDemoImages($maintenance1, [
            ['caption' => 'Pencucian AC Indoor', 'color' => [60, 100, 160]],
            ['caption' => 'Pencucian AC Outdoor', 'color' => [160, 120, 60]],
            ['caption' => 'Kartu Perawatan', 'color' => [100, 90, 160]],
        ], $teknisi);

        $maintenance2 = MaintenanceReport::create([
            'nomor_laporan' => $reportNumber(2, 'PRW'),
            'asset_id' => $assets['AC-010']->id,
            'pelapor_user_id' => $teknisi->id,
            'vendor_id' => $vendors['CV Service AC Borneo']->id,
            'jenis_pekerjaan' => 'Cek tekanan freon & kebersihan filter',
            'uraian_pekerjaan' => 'Pengecekan tekanan freon, pembersihan filter, dan pengetesan suhu output.',
            'tanggal_pelaksanaan' => now()->subDays(12),
            'biaya' => 225000,
            'biaya_jasa' => 175000,
            'status' => ReportStatus::Diverifikasi->value,
            'verified_at' => now()->subDays(10),
            'catatan' => 'Menunggu persetujuan akhir.',
        ]);

        $this->attachDemoImages($maintenance2, [
            ['caption' => 'Pencucian AC Indoor', 'color' => [120, 60, 160]],
            ['caption' => 'Pencucian AC Outdoor', 'color' => [60, 160, 140]],
            ['caption' => 'Kartu Perawatan', 'color' => [160, 90, 60]],
        ], $teknisi);

        // Laporan perawatan dua bagian: satu dokumen dengan 2 aset berbeda.
        $maintenance3 = MaintenanceReport::create([
            'nomor_laporan' => $reportNumber(3, 'PRW'),
            'asset_id' => $assets['AC-006']->id,
            'pelapor_user_id' => $teknisi->id,
            'vendor_id' => $vendors['PT AC Sejahtera']->id,
            'jenis_pekerjaan' => 'Pencucian AC Indoor & Outdoor',
            'uraian_pekerjaan' => 'Pembersihan filter, evaporator, kondensor, dan pengecekan tekanan freon.',
            'tanggal_pelaksanaan' => now()->subDays(20),
            'biaya' => 300000,
            'biaya_jasa' => 200000,
            'status' => ReportStatus::Disetujui->value,
            'verified_at' => now()->subDays(19),
            'approved_at' => now()->subDays(18),
            'catatan' => 'Dua unit AC pada gedung yang sama dikerjakan sekaligus.',
        ]);

        $maintenance3->items()->create([
            'bagian' => 2,
            'asset_id' => $assets['AC-007']->id,
            'jenis_pekerjaan' => 'Pencucian AC Indoor & Outdoor',
            'uraian_pekerjaan' => 'Pembersihan filter dan pengecekan kebocoran freon.',
            'tanggal_pelaksanaan' => now()->subDays(20),
            'biaya' => 250000,
            'biaya_jasa' => 150000,
            'sort_order' => 1,
        ]);

        $this->attachDemoImages($maintenance3, [
            ['caption' => 'Pencucian AC Indoor', 'color' => [60, 130, 90]],
            ['caption' => 'Pencucian AC Outdoor', 'color' => [130, 90, 60]],
            ['caption' => 'Kartu Perawatan', 'color' => [90, 60, 130]],
            ['caption' => 'Pencucian AC Indoor (Bagian 2)', 'slot_key' => 'indoor_cleaning_2', 'color' => [60, 100, 160]],
            ['caption' => 'Pencucian AC Outdoor (Bagian 2)', 'slot_key' => 'outdoor_cleaning_2', 'color' => [160, 120, 60]],
            ['caption' => 'Kartu Perawatan (Bagian 2)', 'slot_key' => 'maintenance_card_2', 'color' => [100, 90, 160]],
        ], $teknisi);
    }

    /**
     * Membuat gambar demo (blok warna) dan melampirkannya ke laporan.
     *
     * @param  array<int, array{caption: string, color?: array<int, int>, slot_key?: string|null}>  $items
     */
    private function attachDemoImages($report, array $items, User $uploader): void
    {
        $disk = Storage::disk('public');

        foreach ($items as $index => $item) {
            $name = sprintf('%s-%d.png', Str::slug($report->nomor_laporan), $index + 1);
            $path = "reports/demo/{$name}";

            $image = imagecreatetruecolor(640, 360);
            [$r, $g, $b] = $item['color'] ?? [120, 120, 120];
            imagefill($image, 0, 0, imagecolorallocate($image, $r, $g, $b));
            $white = imagecolorallocate($image, 255, 255, 255);
            imagestring($image, 5, 20, 160, $item['caption'], $white);

            ob_start();
            imagepng($image);
            $png = ob_get_clean();
            imagedestroy($image);

            $disk->put($path, $png);

            $report->attachments()->create([
                'category' => $report instanceof RepairReport ? 'repair_evidence' : 'maintenance_evidence',
                'slot_key' => $item['slot_key'] ?? ($report instanceof MaintenanceReport ? ['indoor_cleaning', 'outdoor_cleaning', 'maintenance_card'][$index] ?? null : null),
                'caption' => $item['caption'],
                'file_path' => $path,
                'original_name' => $name,
                'mime_type' => 'image/png',
                'file_size' => strlen($png),
                'sort_order' => $index,
                'uploaded_by_user_id' => $uploader->id,
            ]);
        }
    }
}