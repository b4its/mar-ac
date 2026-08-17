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
    /**
     * Jumlah data demo per tabel domain. Tabel users/akun sengaja dikecualikan.
     */
    private const TARGET = 100;

    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $buildings = $this->seedBuildings();
        $rooms = $this->seedRooms($buildings);
        $departments = $this->seedDepartments();
        $vendors = $this->seedVendors();
        $assets = $this->seedAssets($buildings, $rooms, $departments);

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

        $damages = $this->seedDamageReports($admin, $teknisi, $assets);
        $this->seedRepairReports($admin, $teknisi, $assets, $vendors, $damages);
        $this->seedMaintenanceReports($admin, $teknisi, $assets, $vendors);
        $this->seedJadwal($admin, $assets);
    }

    /**
     * 100 gedung: A..Z lalu Gedung 27..Gedung 100.
     *
     * @return array<int, Building>
     */
    private function seedBuildings(): array
    {
        $rows = [];
        for ($i = 1; $i <= self::TARGET; $i++) {
            $code = $i <= 26 ? chr(64 + $i) : 'G'.$i;
            $rows[] = [
                'nama_gedung' => $i <= 26 ? 'Gedung '.chr(64 + $i) : 'Gedung '.$i,
                'kode_gedung' => $code,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Building::insert($rows);

        return Building::query()->orderBy('id')->get()->all();
    }

    /**
     * 100 ruangan: satu ruangan utama untuk setiap gedung.
     *
     * @param  array<int, Building>  $buildings
     * @return array<int, Room>
     */
    private function seedRooms(array $buildings): array
    {
        $pool = [
            'Ruang Administrasi', 'Ruang Server', 'Ruang Rapat', 'Ruang Keuangan',
            'Lab Komputer 1', 'Lab Komputer 2', 'Lab Elektro', 'Ruang Guru',
            'Lab Akuntansi', 'Ruang Kelas 1', 'Perpustakaan', 'Aula',
            'Ruang Kelas 2', 'Ruang Dosen', 'Studio', 'Lab Bahasa',
            'Gudang', 'Ruang Arsip', 'Lobby', 'Lab Kimia',
        ];

        $rows = [];
        foreach ($buildings as $index => $building) {
            $nama = $pool[$index % count($pool)];
            if ($index >= count($pool)) {
                $nama .= ' '.intdiv($index, count($pool)) + 1;
            }

            $rows[] = [
                'building_id' => $building->id,
                'nama_ruangan' => $nama,
                'kode_ruangan' => $building->kode_gedung.'-1',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Room::insert($rows);

        return Room::query()->orderBy('id')->get()->all();
    }

    /**
     * 100 jurusan: 20 nama dasar dikalikan nomor angkatan, 5 nama pertama
     * dipertahankan persis agar sama dengan data demo sebelumnya.
     *
     * @return array<int, Department>
     */
    private function seedDepartments(): array
    {
        $pool = [
            'Teknik Informatika', 'Teknik Arsitektur & Sipil', 'Akuntansi',
            'Teknik Elektro', 'Teknik Mesin', 'Administrasi Bisnis',
            'Teknik Kimia', 'Teknik Sipil', 'Teknologi Listrik',
            'Rekayasa Perangkat Lunak', 'Pariwisata', 'Kebidanan',
            'Keperawatan', 'Teknik Pengelasan', 'Teknik Otomotif',
            'Desain Grafis', 'Sistem Informasi', 'Bisnis Digital',
            'Manajemen Logistik', 'Agribisnis',
        ];

        $rows = [];
        for ($i = 1; $i <= self::TARGET; $i++) {
            $nama = $pool[($i - 1) % count($pool)];
            if ($i > count($pool)) {
                $nama .= ' '.intdiv($i - 1, count($pool)) + 1;
            }

            $rows[] = [
                'nama_jurusan' => $nama,
                'kode_jurusan' => Str::upper(Str::slug($nama, '-')),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Department::insert($rows);

        return Department::query()->orderBy('id')->get()->all();
    }

    /**
     * 100 vendor jasa AC dengan kontak, telepon, dan alamat yang beragam.
     *
     * @return array<int, Vendor>
     */
    private function seedVendors(): array
    {
        $namaPool = [
            'PT AC Sejahtera', 'CV Teknik Mandiri', 'PT Dingin Sejuk Abadi',
            'CV Service AC Borneo', 'PT Pendingin Nusantara', 'CV Jaya Teknik',
            'PT Mitra Sejuk', 'CV Karya AC', 'PT Teknologi Pendingin', 'UD Makmur Jaya',
            'PT Bintang Timur', 'CV Global Servis', 'PT Cipta Karya', 'CV Anugerah',
            'PT Surya Teknik', 'UD Sumber Rejeki', 'PT Nusa Pendingin', 'CV Prima Servis',
            'PT Indah Karya', 'UD Setia Jaya',
        ];
        $kontakPool = [
            'Budi Santoso', 'Agus Wijaya', 'Rina Kartika', 'Dedi Hartono', 'Siti Rahma',
            'Andi Pratama', 'Dewi Lestari', 'Eko Susanto', 'Fitri Handayani', 'Guntur Saputra',
        ];
        $jalanPool = [
            'Jl. Pahlawan', 'Jl. S. Parman', 'Jl. Gatot Subroto', 'Jl. AW Syahrani',
            'Jl. Juanda', 'Jl. Diponegoro', 'Jl. Sudirman', 'Jl. Agus Salim',
        ];

        $rows = [];
        for ($i = 1; $i <= self::TARGET; $i++) {
            $nama = $namaPool[($i - 1) % count($namaPool)];
            if ($i > count($namaPool)) {
                $nama .= ' '.intdiv($i - 1, count($namaPool)) + 1;
            }

            $rows[] = [
                'nama_vendor' => $nama,
                'kontak' => $kontakPool[($i - 1) % count($kontakPool)],
                'telepon' => sprintf('08%02d-%04d-%04d', 12 + (($i - 1) % 8), ($i * 37) % 10000, ($i * 53) % 10000),
                'alamat' => $jalanPool[($i - 1) % count($jalanPool)].' No. '.(($i * 7) % 150 + 1).', Samarinda',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Vendor::insert($rows);

        return Vendor::query()->orderBy('id')->get()->all();
    }

    /**
     * 500+ aset AC: setiap ruangan berisi 5 aset agar daftar alat/mesin pada
     * form tetap bervariasi setelah filter lokasi dipilih. 16 aset pertama
     * mempertahankan data demo lama, sisanya dibangkitkan dengan kombinasi
     * merk/kapasitas/tahun/status yang beragam. Satu aset sengaja tanpa ruangan.
     *
     * @param  array<int, Building>  $buildings
     * @param  array<int, Room>  $rooms
     * @param  array<int, Department>  $departments
     * @return array<string, Asset>
     */
    private function seedAssets(array $buildings, array $rooms, array $departments): array
    {
        $byName = fn (string $nama) => collect($departments)->first(fn (Department $d) => $d->nama_jurusan === $nama);

        $roomByIndex = fn (int $index) => $rooms[$index % count($rooms)] ?? null;

        $legacy = [
            ['AC-001', 'AC Split 2 PK', 'Daikin', '2 PK', '2022', 'baik', 'INV-2022-0001', 0, 'Akuntansi', now()->subDays(30)],
            ['AC-002', 'AC Split 1,5 PK', 'Panasonic', '1,5 PK', '2021', 'baik', 'INV-2021-0002', 1, 'Teknik Informatika', now()->subDays(6)],
            ['AC-003', 'AC Cassette 3 PK', 'Gree', '3 PK', '2023', 'baik', 'INV-2023-0003', 2, 'Akuntansi', now()->subDays(55)],
            ['AC-004', 'AC Split 1 PK', 'Sharp', '1 PK', '2019', 'rusak_ringan', 'INV-2019-0004', 3, 'Akuntansi', null],
            ['AC-005', 'AC Standing 5 PK', 'LG', '5 PK', '2020', 'rusak_sedang', 'INV-2020-0005', 4, 'Akuntansi', now()->subDays(120)],
            ['AC-006', 'AC Split 2 PK', 'Daikin', '2 PK', '2023', 'baik', 'INV-2023-0006', 5, 'Teknik Informatika', now()->subDays(20)],
            ['AC-007', 'AC Split 1 PK', 'Samsung', '1 PK', '2022', 'baik', 'INV-2022-0007', 6, 'Teknik Informatika', now()->subDays(20)],
            ['AC-008', 'AC Window 1,5 PK', 'Mitsubishi', '1,5 PK', '2018', 'rusak_berat', 'INV-2018-0008', 7, 'Teknik Elektro', null],
            ['AC-009', 'AC Split 2 PK', 'Carrier', '2 PK', '2021', 'rusak_ringan', 'INV-2021-0009', 8, 'Teknik Arsitektur & Sipil', now()->subDays(95)],
            ['AC-010', 'AC Cassette 3 PK', 'Gree', '3 PK', '2024', 'baik', 'INV-2024-0010', 9, 'Teknik Elektro', now()->subDays(12)],
            ['AC-011', 'AC Split 1,5 PK', 'Panasonic', '1,5 PK', '2022', 'baik', 'INV-2022-0011', 10, 'Akuntansi', now()->subDays(40)],
            ['AC-012', 'AC Split 1 PK', 'Sharp', '1 PK', '2020', 'baik', 'INV-2020-0012', 11, 'Teknik Arsitektur & Sipil', now()->subDays(70)],
            ['AC-013', 'AC Standing 3 PK', 'LG', '3 PK', '2019', 'rusak_ringan', 'INV-2019-0013', 12, 'Teknik Arsitektur & Sipil', now()->subDays(150)],
            ['AC-014', 'AC Duct 4 PK', 'Daikin', '4 PK', '2024', 'baik', 'INV-2024-0014', 13, 'Teknik Mesin', now()->subDays(10)],
            ['AC-015', 'AC Split 2 PK', 'Samsung', '2 PK', '2021', 'rusak_sedang', 'INV-2021-0015', 14, 'Akuntansi', now()->subDays(200)],
            ['AC-016', 'AC Portable 1 PK', 'Midea', '1 PK', '2024', 'baik', 'INV-2024-0016', null, 'Teknik Mesin', null],
        ];

        $assets = [];
        foreach ($legacy as [$kode, $nama, $merk, $kapasitas, $tahun, $status, $inventaris, $roomIndex, $jurusanNama, $perawatanTerakhir]) {
            $ruangan = $roomIndex !== null ? $roomByIndex($roomIndex) : null;
            $jurusan = $jurusanNama !== null ? $byName($jurusanNama) : null;

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
                'last_maintenance_date' => $perawatanTerakhir?->toDateString(),
            ]);
        }

        // Setiap ruangan diisi 5 aset. Kelima aset dalam satu ruangan memakai
        // jurusan yang sama, sehingga setelah filter gedung → jurusan → ruangan
        // dipilih, daftar alat/mesin menampilkan 5 pilihan (bukan hanya satu).
        $types = ['AC Split 1 PK', 'AC Split 1,5 PK', 'AC Split 2 PK', 'AC Cassette 3 PK', 'AC Standing 5 PK'];
        $kapasitasPool = ['1 PK', '1,5 PK', '2 PK', '3 PK', '5 PK'];
        $merks = ['Daikin', 'Panasonic', 'Gree', 'Sharp', 'LG', 'Samsung', 'Mitsubishi', 'Carrier', 'Midea', 'Toshiba'];
        $statuses = ['baik', 'baik', 'baik', 'rusak_ringan', 'rusak_sedang', 'rusak_berat'];

        // Indeks jurusan (0=TI, 1=TAS, 2=AK, 3=TE, 4=TM) untuk ruangan 0..14
        // agar sama dengan jurusan aset legacy yang sudah ada di ruangan itu.
        $legacyDeptIndex = [2, 0, 2, 2, 2, 0, 0, 3, 1, 3, 2, 1, 1, 4, 2];

        $rows = [];
        $nextKode = 17;
        $counter = 1;

        foreach ($rooms as $roomIndex => $room) {
            // Ruangan 0..14 sudah memiliki 1 aset legacy (AC-001..AC-015).
            $existing = $roomIndex < 15 ? 1 : 0;
            $needed = 5 - $existing;

            $deptIndex = $roomIndex < 15 ? $legacyDeptIndex[$roomIndex] : $roomIndex;

            for ($j = 0; $j < $needed; $j++) {
                $i = $nextKode++;
                $tahun = 2016 + (($counter - 1) % 9);
                $perawatanTerakhir = $counter % 10 === 0 ? null : now()->subDays(($counter * 3) % 300 + 1);

                $rows[] = [
                    'nama_alat' => $types[($counter - 1) % count($types)],
                    'jenis_alat' => 'Pendingin Ruangan',
                    'kode_alat' => sprintf('AC-%03d', $i),
                    'no_inventaris' => sprintf('INV-%d-%04d', $tahun, $i),
                    'room_id' => $room->id,
                    'department_id' => $departments[$deptIndex]->id,
                    'kapasitas' => $kapasitasPool[($counter - 1) % count($kapasitasPool)],
                    'merk' => $merks[($counter - 1) % count($merks)],
                    'tahun_pemakaian' => $tahun,
                    'status' => $statuses[($counter - 1) % count($statuses)],
                    'last_maintenance_date' => $perawatanTerakhir?->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $counter++;
            }
        }

        Asset::insert($rows);

        return Asset::query()->orderBy('id')->get()->keyBy('kode_alat')->all();
    }

    /**
     * 100 laporan kerusakan dengan tingkat dan status yang beragam.
     *
     * @param  array<string, Asset>  $assets
     * @return array<int, DamageReport>
     */
    private function seedDamageReports(User $admin, User $teknisi, array $assets): array
    {
        $assetList = array_values($assets);
        $tingkat = ['ringan', 'sedang', 'berat'];
        $jenisPool = [
            'AC tidak dingin', 'Kompresor mati', 'Bunyi berisik pada outdoor',
            'Air menetes dari indoor', 'Remote tidak berfungsi', 'Kebocoran freon',
        ];
        $uraianPool = [
            'Suhu ruangan tidak turun meskipun AC sudah dinyalakan lama.',
            'AC mati mendadak dan tidak dapat dinyalakan kembali.',
            'Muncul bunyi berisik saat AC dinyalakan, kemungkinan fan longgar.',
            'Terdapat tetesan air dari unit indoor saat AC beroperasi.',
            'Remote tidak merespons, indikator pada unit tetap menyala.',
            'Tekanan freon menurun drastis dalam beberapa hari terakhir.',
        ];

        $year = now()->format('Y');
        $rows = [];

        for ($i = 1; $i <= self::TARGET; $i++) {
            $status = match ($i % 4) {
                1 => DamageReportStatus::Ditolak,
                2 => DamageReportStatus::Disetujui,
                3 => DamageReportStatus::Selesai,
                default => DamageReportStatus::Dilaporkan,
            };

            $isApproved = $status !== DamageReportStatus::Dilaporkan;

            $rows[] = [
                'nomor_laporan' => sprintf('%03d/UPA.PP/KSR/%s', $i, $year),
                'asset_id' => $assetList[($i - 1) % count($assetList)]->id,
                'pelapor_user_id' => $teknisi->id,
                'tingkat_kerusakan' => $tingkat[($i - 1) % count($tingkat)],
                'jenis_kerusakan' => $jenisPool[($i - 1) % count($jenisPool)],
                'uraian_kerusakan' => $uraianPool[($i - 1) % count($uraianPool)],
                'tanggal_laporan' => now()->subDays($i),
                'status' => $status->value,
                'approved_at' => $isApproved ? now()->subDays(max(0, $i - 1)) : null,
                'approved_by_user_id' => $isApproved ? $admin->id : null,
                'catatan' => $isApproved ? 'Sudah ditindaklanjuti oleh tim teknis.' : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DamageReport::insert($rows);

        return DamageReport::query()->orderBy('id')->get()->all();
    }

    /**
     * 100 laporan perbaikan, masing-masing merujuk satu laporan kerusakan.
     *
     * @param  array<string, Asset>  $assets
     * @param  array<int, Vendor>  $vendors
     * @param  array<int, DamageReport>  $damages
     */
    private function seedRepairReports(User $admin, User $teknisi, array $assets, array $vendors, array $damages): void
    {
        $jenisPool = [
            'Penggantian kompresor AC', 'Perbaikan fan blower outdoor',
            'Perbaikan kebocoran freon', 'Penggantian PCB kontrol',
        ];
        $uraianPool = [
            'Komponen diganti dengan unit baru, lalu diuji selama 2 jam.',
            'Baut dikencangkan dan bearing motor diberi pelumas.',
            'Sambungan pipa diganti dan freon diisi ulang sesuai tekanan standar.',
            'Modul kontrol diganti dan sistem dikalibrasi ulang.',
        ];

        $year = now()->format('Y');
        $rows = [];

        for ($i = 1; $i <= self::TARGET; $i++) {
            $damage = $damages[$i - 1];
            $status = match ($i % 3) {
                1 => RepairStatus::Disetujui,
                2 => RepairStatus::Revisi,
                default => RepairStatus::Diajukan,
            };

            $selesai = $status === RepairStatus::Disetujui;

            $rows[] = [
                'nomor_laporan' => sprintf('%03d/UPA.PP/PRB/%s', $i, $year),
                'damage_report_id' => $damage->id,
                'asset_id' => $damage->asset_id,
                'vendor_id' => $vendors[($i - 1) % count($vendors)]->id,
                'pelapor_user_id' => $teknisi->id,
                'teknisi_user_id' => $teknisi->id,
                'jenis_pekerjaan' => $jenisPool[($i - 1) % count($jenisPool)],
                'uraian_pekerjaan' => $uraianPool[($i - 1) % count($uraianPool)],
                'tanggal_pelaksanaan' => now()->subDays($i + 1),
                'tanggal_selesai' => $selesai ? now()->subDays(max(0, $i - 1)) : null,
                'biaya' => 200000 + (($i * 37) % 20) * 50000,
                'biaya_jasa' => 100000 + (($i * 53) % 10) * 25000,
                'status' => $status->value,
                'verifikator_user_id' => $selesai ? $admin->id : null,
                'verified_at' => $selesai ? now()->subDays(max(0, $i - 1)) : null,
                'catatan' => $selesai ? 'Selesai dikerjakan sesuai jadwal kerja sama vendor.' : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        RepairReport::insert($rows);
    }

    /**
     * 100 laporan perawatan. Setiap laporan memiliki satu bagian kedua (item)
     * dan satu lampiran demo, kecuali laporan khusus nomor 3 yang memuat enam
     * lampiran dua bagian seperti data demo sebelumnya.
     *
     * @param  array<string, Asset>  $assets
     * @param  array<int, Vendor>  $vendors
     */
    private function seedMaintenanceReports(User $admin, User $teknisi, array $assets, array $vendors): void
    {
        $assetList = array_values($assets);
        $jenisPool = [
            'Pencucian AC Indoor & Outdoor', 'Cek tekanan freon & kebersihan filter',
            'Penggantian filter udara', 'Pembersihan evaporator & kondensor',
        ];
        $uraianPool = [
            'Pembersihan filter, evaporator, kondensor, dan pengecekan tekanan freon.',
            'Pengecekan tekanan freon, pembersihan filter, dan pengetesan suhu output.',
            'Filter udara diganti dengan unit baru sesuai jadwal perawatan.',
            'Evaporator dan kondensor dibersihkan dari debu yang menumpuk.',
        ];

        $year = now()->format('Y');

        for ($i = 1; $i <= self::TARGET; $i++) {
            $status = match ($i % 3) {
                1 => ReportStatus::Diverifikasi,
                2 => ReportStatus::Disetujui,
                default => ReportStatus::Diajukan,
            };

            $asset = $assetList[($i - 1) % count($assetList)];

            $report = MaintenanceReport::create([
                'nomor_laporan' => sprintf('%03d/UPA.PP/PRW/%s', $i, $year),
                'asset_id' => $asset->id,
                'pelapor_user_id' => $teknisi->id,
                'vendor_id' => $vendors[($i - 1) % count($vendors)]->id,
                'jenis_pekerjaan' => $jenisPool[($i - 1) % count($jenisPool)],
                'uraian_pekerjaan' => $uraianPool[($i - 1) % count($uraianPool)],
                'tanggal_pelaksanaan' => now()->subDays(($i * 2) % 300 + 1),
                'biaya' => 150000 + (($i * 41) % 10) * 25000,
                'biaya_jasa' => 100000 + (($i * 29) % 8) * 25000,
                'status' => $status->value,
                'verified_at' => $status !== ReportStatus::Diajukan ? now()->subDays(($i * 2) % 300) : null,
                'approved_at' => $status === ReportStatus::Disetujui ? now()->subDays(max(0, ($i * 2) % 300 - 1)) : null,
                'catatan' => $status === ReportStatus::Disetujui ? 'Disetujui oleh atasan.' : null,
            ]);

            // Bagian kedua (item) agar tabel maintenance_report_items penuh.
            $report->items()->create([
                'bagian' => 2,
                'asset_id' => $assetList[$i % count($assetList)]->id,
                'jenis_pekerjaan' => $jenisPool[$i % count($jenisPool)],
                'uraian_pekerjaan' => 'Pekerjaan lanjutan pada unit kedua di lokasi yang sama.',
                'tanggal_pelaksanaan' => now()->subDays(($i * 2) % 300 + 1),
                'biaya' => 100000 + (($i * 17) % 8) * 25000,
                'biaya_jasa' => 75000 + (($i * 13) % 6) * 25000,
                'sort_order' => 0,
            ]);

            if ($i === 3) {
                $this->attachDemoImages($report, [
                    ['caption' => 'Pencucian AC Indoor', 'color' => [60, 130, 90]],
                    ['caption' => 'Pencucian AC Outdoor', 'color' => [130, 90, 60]],
                    ['caption' => 'Kartu Perawatan', 'color' => [90, 60, 130]],
                    ['caption' => 'Pencucian AC Indoor', 'slot_key' => 'indoor_cleaning_2', 'color' => [60, 100, 160]],
                    ['caption' => 'Pencucian AC Outdoor', 'slot_key' => 'outdoor_cleaning_2', 'color' => [160, 120, 60]],
                    ['caption' => 'Kartu Perawatan', 'slot_key' => 'maintenance_card_2', 'color' => [100, 90, 160]],
                ], $teknisi);
            } elseif ($i >= 4 && $i <= 97) {
                $this->attachDemoImages($report, [
                    ['caption' => 'Pencucian AC Indoor', 'color' => [60, 100, 160]],
                ], $teknisi);
            }
        }
    }

    /**
     * 100 jadwal pemeliharaan dengan status terjadwal, selesai, dan dibatalkan.
     *
     * @param  array<string, Asset>  $assets
     */
    private function seedJadwal(User $admin, array $assets): void
    {
        $assetList = array_values($assets);
        $jenisPool = [
            'Pencucian AC Indoor & Outdoor', 'Cek tekanan freon & kebersihan filter',
            'Penggantian filter udara indoor', 'Pelumasan motor blower',
        ];

        $rows = [];
        for ($i = 1; $i <= self::TARGET; $i++) {
            $status = match ($i % 3) {
                1 => JadwalStatus::Selesai,
                2 => JadwalStatus::Dibatalkan,
                default => JadwalStatus::Terjadwal,
            };

            $rows[] = [
                'asset_id' => $assetList[($i - 1) % count($assetList)]->id,
                'tanggal_jadwal' => $status === JadwalStatus::Terjadwal
                    ? now()->addDays(($i % 90) + 1)
                    : now()->subDays(($i % 90) + 1),
                'jenis_pekerjaan' => $jenisPool[($i - 1) % count($jenisPool)],
                'catatan' => match ($status) {
                    JadwalStatus::Terjadwal => 'Jadwal rutin bulanan kerja sama vendor.',
                    JadwalStatus::Selesai => 'Sudah dilaksanakan oleh teknisi.',
                    JadwalStatus::Dibatalkan => 'Dibatalkan karena jadwal bentrok.',
                },
                'status' => $status->value,
                'created_by_user_id' => $admin->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        JadwalPemeliharaan::insert($rows);
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

            $image = imagecreatetruecolor(480, 270);
            [$r, $g, $b] = $item['color'] ?? [120, 120, 120];
            imagefill($image, 0, 0, imagecolorallocate($image, $r, $g, $b));
            $white = imagecolorallocate($image, 255, 255, 255);
            imagestring($image, 5, 20, 120, $item['caption'], $white);

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
