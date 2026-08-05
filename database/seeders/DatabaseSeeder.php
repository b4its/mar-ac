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

        $gedungA = Building::create(['nama_gedung' => 'Gedung A', 'kode_gedung' => 'A']);
        $gedungB = Building::create(['nama_gedung' => 'Gedung B', 'kode_gedung' => 'B']);

        $ruangA1 = Room::create(['building_id' => $gedungA->id, 'nama_ruangan' => 'Ruang Administrasi', 'kode_ruangan' => 'A-1']);
        $ruangA2 = Room::create(['building_id' => $gedungA->id, 'nama_ruangan' => 'Ruang Server', 'kode_ruangan' => 'A-2']);
        $ruangB1 = Room::create(['building_id' => $gedungB->id, 'nama_ruangan' => 'Lab Komputer 1', 'kode_ruangan' => 'B-1']);

        $ti = Department::create(['nama_jurusan' => 'Teknik Informatika', 'kode_jurusan' => 'TI']);
        $tas = Department::create(['nama_jurusan' => 'Teknik Arsitektur & Sipil', 'kode_jurusan' => 'TAS']);
        $akuntansi = Department::create(['nama_jurusan' => 'Akuntansi', 'kode_jurusan' => 'AK']);

        $vendor1 = Vendor::create([
            'nama_vendor' => 'PT AC Sejahtera',
            'kontak' => 'Budi Santoso',
            'telepon' => '0812-3456-7890',
            'alamat' => 'Jl. Pahlawan No. 12, Samarinda',
        ]);
        $vendor2 = Vendor::create([
            'nama_vendor' => 'CV Teknik Mandiri',
            'kontak' => 'Agus Wijaya',
            'telepon' => '0813-9876-5432',
            'alamat' => 'Jl. S. Parman No. 45, Samarinda',
        ]);

        Asset::create([
            'nama_alat' => 'AC Split 2 PK',
            'jenis_alat' => 'Pendingin Ruangan',
            'kode_alat' => 'AC-001',
            'no_inventaris' => 'INV-2024-0001',
            'room_id' => $ruangA1->id,
            'department_id' => $ti->id,
            'kapasitas' => '2 PK',
            'merk' => 'Daikin',
            'tahun_pemakaian' => '2022',
            'status' => 'baik',
        ]);

        Asset::create([
            'nama_alat' => 'AC Split 1,5 PK',
            'jenis_alat' => 'Pendingin Ruangan',
            'kode_alat' => 'AC-002',
            'no_inventaris' => 'INV-2024-0002',
            'room_id' => $ruangA2->id,
            'department_id' => $ti->id,
            'kapasitas' => '1,5 PK',
            'merk' => 'Panasonic',
            'tahun_pemakaian' => '2021',
            'status' => 'baik',
        ]);

        Asset::create([
            'nama_alat' => 'AC Cassette 3 PK',
            'jenis_alat' => 'Pendingin Ruangan',
            'kode_alat' => 'AC-003',
            'no_inventaris' => 'INV-2024-0003',
            'room_id' => $ruangB1->id,
            'department_id' => $tas->id,
            'kapasitas' => '3 PK',
            'merk' => 'Gree',
            'tahun_pemakaian' => '2023',
            'status' => 'baik',
        ]);

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

        $this->demoReports($admin, $teknisi, $vendor1, $vendor2);
        $this->demoJadwal($admin);
    }

    public function demoJadwal(User $admin): void
    {
        if (JadwalPemeliharaan::exists()) {
            return;
        }

        $ac2 = Asset::where('kode_alat', 'AC-001')->first();
        $ac3 = Asset::where('kode_alat', 'AC-003')->first();

        if (! $ac2 || ! $ac3) {
            return;
        }

        JadwalPemeliharaan::create([
            'asset_id' => $ac2->id,
            'tanggal_jadwal' => now()->addDays(7),
            'jenis_pekerjaan' => 'Pencucian AC Indoor & Outdoor',
            'catatan' => 'Jadwal rutin bulanan kerja sama vendor.',
            'status' => JadwalStatus::Terjadwal->value,
            'created_by_user_id' => $admin->id,
        ]);

        JadwalPemeliharaan::create([
            'asset_id' => $ac3->id,
            'tanggal_jadwal' => now()->addDays(14),
            'jenis_pekerjaan' => 'Cek tekanan freon & kebersihan filter',
            'catatan' => 'Jadwal rutin dua mingguan.',
            'status' => JadwalStatus::Terjadwal->value,
            'created_by_user_id' => $admin->id,
        ]);
    }

    public function demoReports(User $admin, User $teknisi, Vendor $vendor1, Vendor $vendor2): void
    {
        if (DamageReport::exists()) {
            return;
        }

        $year = now()->format('Y');

        $damage = DamageReport::create([
            'nomor_laporan' => sprintf('001/UPA.PP/KSR/%s', $year),
            'asset_id' => Asset::where('kode_alat', 'AC-001')->first()->id,
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

        $repair = RepairReport::create([
            'nomor_laporan' => sprintf('001/UPA.PP/PRB/%s', $year),
            'damage_report_id' => $damage->id,
            'asset_id' => $damage->asset_id,
            'pelapor_user_id' => $teknisi->id,
            'teknisi_user_id' => $teknisi->id,
            'vendor_id' => $vendor1->id,
            'jenis_pekerjaan' => 'Penggantian kompresor AC',
            'uraian_pekerjaan' => 'Kompresor diganti dengan unit baru, freon diisi ulang, dan unit diuji selama 2 jam.',
            'tanggal_pelaksanaan' => now()->subDays(2),
            'biaya' => 2500000,
            'biaya_jasa' => 500000,
            'status' => RepairStatus::Diajukan->value,
        ]);

        $this->attachDemoImages($repair, [
            ['caption' => 'Kondisi kompresor lama', 'color' => [150, 60, 60]],
            ['caption' => 'Kompresor baru terpasang', 'color' => [60, 130, 90]],
        ], $teknisi);

        $maintenance = MaintenanceReport::create([
            'nomor_laporan' => sprintf('001/UPA.PP/PRW/%s', $year),
            'asset_id' => Asset::where('kode_alat', 'AC-002')->first()->id,
            'pelapor_user_id' => $teknisi->id,
            'vendor_id' => $vendor2->id,
            'jenis_pekerjaan' => 'Pencucian AC Indoor & Outdoor',
            'uraian_pekerjaan' => 'Pembersihan filter, evaporator, kondensor, dan pengecekan tekanan freon.',
            'tanggal_pelaksanaan' => now()->subDays(6),
            'biaya' => 150000,
            'biaya_jasa' => 100000,
            'status' => ReportStatus::Diajukan->value,
        ]);

        $this->attachDemoImages($maintenance, [
            ['caption' => 'Pencucian AC Indoor', 'color' => [60, 100, 160]],
            ['caption' => 'Pencucian AC Outdoor', 'color' => [160, 120, 60]],
            ['caption' => 'Kartu Perawatan', 'color' => [100, 90, 160]],
        ], $teknisi);
    }

    private function attachDemoImages($report, array $items, User $uploader): void
    {
        $disk = Storage::disk('public');

        foreach ($items as $index => $item) {
            $name = sprintf('%s-%d.png', Str::slug($report->nomor_laporan), $index + 1);
            $path = "reports/demo/{$name}";

            $image = imagecreatetruecolor(640, 360);
            [$r, $g, $b] = $item['color'];
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
                'slot_key' => $report instanceof MaintenanceReport ? ['indoor_cleaning', 'outdoor_cleaning', 'maintenance_card'][$index] : null,
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
