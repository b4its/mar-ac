<?php

use App\Livewire\LokasiFilter;
use App\Livewire\SearchableSelect;
use App\Models\Asset;
use App\Models\Building;
use App\Models\Department;
use App\Models\MaintenanceReport;
use App\Models\Room;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function makeTestImage(string $name, int $size = 128): UploadedFile
{
    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
    $name = str_ends_with($name, '.png') ? $name : $name.'.png';

    return UploadedFile::fake()->createWithContent($name, str_pad($png, $size, '0'), 'image/png');
}

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);

    $teknisi = User::factory()->create(['name' => 'Teknisi']);
    $teknisi->assignRole('teknisi');
    $this->teknisi = $teknisi;

    $this->gedungA = Building::create(['nama_gedung' => 'Gedung A', 'kode_gedung' => 'A']);
    $this->gedungB = Building::create(['nama_gedung' => 'Gedung B', 'kode_gedung' => 'B']);

    $this->ruangA1 = Room::create(['building_id' => $this->gedungA->id, 'nama_ruangan' => 'Ruang A1', 'kode_ruangan' => 'A-1']);
    $this->ruangB1 = Room::create(['building_id' => $this->gedungB->id, 'nama_ruangan' => 'Ruang B1', 'kode_ruangan' => 'B-1']);

    $this->ti = Department::create(['nama_jurusan' => 'Teknik Informatika', 'kode_jurusan' => 'TI']);
    $this->akuntansi = Department::create(['nama_jurusan' => 'Akuntansi', 'kode_jurusan' => 'AK']);

    $this->asset1 = Asset::create([
        'nama_alat' => 'AC Split 2 PK',
        'jenis_alat' => 'Pendingin Ruangan',
        'kode_alat' => 'AC-1',
        'no_inventaris' => 'INV-1',
        'room_id' => $this->ruangA1->id,
        'department_id' => $this->ti->id,
        'status' => 'baik',
    ]);

    $this->asset2 = Asset::create([
        'nama_alat' => 'AC Split 1,5 PK',
        'jenis_alat' => 'Pendingin Ruangan',
        'kode_alat' => 'AC-2',
        'no_inventaris' => 'INV-2',
        'room_id' => $this->ruangA1->id,
        'department_id' => $this->ti->id,
        'status' => 'baik',
    ]);

    $this->asset3 = Asset::create([
        'nama_alat' => 'AC Cassette 3 PK',
        'jenis_alat' => 'Pendingin Ruangan',
        'kode_alat' => 'AC-3',
        'no_inventaris' => 'INV-3',
        'room_id' => $this->ruangB1->id,
        'department_id' => $this->akuntansi->id,
        'status' => 'baik',
    ]);
});

it('halaman form perawatan menampilkan dua bagian dan filter lokasi', function () {
    $this->actingAs($this->teknisi)
        ->get(route('laporan.perawatan'))
        ->assertOk()
        ->assertSee('Filter Lokasi Aset')
        ->assertSee('Bagian 1')
        ->assertSee('Bagian 2')
        ->assertSee('+ Tambah Bagian')
        ->assertSee('Kartu Perawatan');
});

it('laporan perawatan dua bagian menyimpan item dan lampiran terpisah', function () {
    $this->actingAs($this->teknisi);

    $this->post(route('laporan.perawatan.store'), [
        'asset_id' => $this->asset1->id,
        'jenis_pekerjaan' => 'Cleaning Indoor & Outdoor AC 1',
        'uraian_pekerjaan' => 'Uraian bagian 1',
        'tanggal_pelaksanaan' => now()->toDateString(),
        'biaya' => '100.000',
        'biaya_jasa' => '50.000',
        'foto_indoor' => makeTestImage('i1'),
        'foto_outdoor' => makeTestImage('o1'),
        'foto_kartu' => makeTestImage('k1'),
        'asset_id_2' => $this->asset2->id,
        'jenis_pekerjaan_2' => 'Cleaning Indoor & Outdoor AC 2',
        'uraian_pekerjaan_2' => 'Uraian bagian 2',
        'tanggal_pelaksanaan_2' => now()->toDateString(),
        'biaya_2' => '200.000',
        'biaya_jasa_2' => '75.000',
        'foto_indoor_2' => makeTestImage('i2'),
        'foto_outdoor_2' => makeTestImage('o2'),
        'foto_kartu_2' => makeTestImage('k2'),
    ])->assertRedirect();

    $report = MaintenanceReport::latest('id')->first();

    expect($report->items)->toHaveCount(1)
        ->and($report->items[0]->bagian)->toBe(2)
        ->and($report->items[0]->asset_id)->toBe($this->asset2->id)
        ->and($report->items[0]->jenis_pekerjaan)->toBe('Cleaning Indoor & Outdoor AC 2')
        ->and((int) $report->items[0]->biaya)->toBe(200000)
        ->and((int) $report->items[0]->biaya_jasa)->toBe(75000);

    expect($report->attachments)->toHaveCount(6)
        ->and($report->attachments()->where('slot_key', 'indoor_cleaning')->exists())->toBeTrue()
        ->and($report->attachments()->where('slot_key', 'outdoor_cleaning')->exists())->toBeTrue()
        ->and($report->attachments()->where('slot_key', 'maintenance_card')->exists())->toBeTrue()
        ->and($report->attachments()->where('slot_key', 'indoor_cleaning_2')->exists())->toBeTrue()
        ->and($report->attachments()->where('slot_key', 'outdoor_cleaning_2')->exists())->toBeTrue()
        ->and($report->attachments()->where('slot_key', 'maintenance_card_2')->exists())->toBeTrue()
        ->and($report->attachments()->where('slot_key', 'indoor_cleaning_2')->first()->caption)->toBe('Pencucian AC Indoor (Bagian 2)')
        ->and($report->attachments()->where('slot_key', 'indoor_cleaning_2')->first()->bagian())->toBe(2);
});

it('bagian kedua tanpa foto ditolak saat asset_id_2 diisi', function () {
    $this->actingAs($this->teknisi);

    $this->post(route('laporan.perawatan.store'), [
        'asset_id' => $this->asset1->id,
        'jenis_pekerjaan' => 'Cleaning AC 1',
        'tanggal_pelaksanaan' => now()->toDateString(),
        'biaya' => '100000',
        'foto_indoor' => makeTestImage('i1'),
        'foto_outdoor' => makeTestImage('o1'),
        'foto_kartu' => makeTestImage('k1'),
        'asset_id_2' => $this->asset2->id,
        'jenis_pekerjaan_2' => 'Cleaning AC 2',
    ])->assertSessionHasErrors(['foto_indoor_2', 'foto_outdoor_2', 'foto_kartu_2']);

    expect(MaintenanceReport::count())->toBe(0);
});

it('bagian kedua tanpa asset diabaikan (tetap satu bagian)', function () {
    $this->actingAs($this->teknisi);

    $this->post(route('laporan.perawatan.store'), [
        'asset_id' => $this->asset1->id,
        'jenis_pekerjaan' => 'Cleaning AC 1',
        'tanggal_pelaksanaan' => now()->toDateString(),
        'biaya' => '100000',
        'foto_indoor' => makeTestImage('i1'),
        'foto_outdoor' => makeTestImage('o1'),
        'foto_kartu' => makeTestImage('k1'),
    ])->assertRedirect();

    $report = MaintenanceReport::latest('id')->first();

    expect($report->items)->toHaveCount(0)
        ->and($report->attachments)->toHaveCount(3);
});

it('filter lokasi bercabang membatasi jurusan dan ruangan', function () {
    $this->actingAs($this->teknisi);

    Livewire::test(LokasiFilter::class)
        ->call('selectBuilding', $this->gedungA->id)
        ->assertSet('buildingId', $this->gedungA->id)
        ->assertSet('departmentId', null)
        ->assertSet('roomId', null)
        ->assertDispatched('lokasi-filter-changed', buildingId: $this->gedungA->id, departmentId: null, roomId: null)
        ->set('openDepartment', true)
        ->assertSee('Teknik Informatika')
        ->assertDontSee('Akuntansi')
        ->call('selectDepartment', $this->ti->id)
        ->assertSet('departmentId', $this->ti->id)
        ->set('openRoom', true)
        ->assertSee('Ruang A1')
        ->assertDontSee('Ruang B1')
        ->call('selectRoom', $this->ruangA1->id)
        ->assertSet('roomId', $this->ruangA1->id)
        ->assertDispatched('lokasi-filter-changed', buildingId: $this->gedungA->id, departmentId: $this->ti->id, roomId: $this->ruangA1->id);
});

it('clear gedung menghapus seluruh filter lokasi', function () {
    $this->actingAs($this->teknisi);

    Livewire::test(LokasiFilter::class)
        ->call('selectBuilding', $this->gedungA->id)
        ->call('selectDepartment', $this->ti->id)
        ->call('selectRoom', $this->ruangA1->id)
        ->assertSet('roomId', $this->ruangA1->id)
        ->call('clearBuilding')
        ->assertSet('buildingId', null)
        ->assertSet('departmentId', null)
        ->assertSet('roomId', null)
        ->assertDispatched('lokasi-filter-changed', buildingId: null, departmentId: null, roomId: null);
});

it('searchable select aset terfilter oleh event lokasi', function () {
    $this->actingAs($this->teknisi);

    Livewire::test(SearchableSelect::class, [
        'type' => 'asset',
        'name' => 'asset_id',
        'label' => 'Aset',
    ])
        ->dispatch('lokasi-filter-changed', buildingId: $this->gedungA->id, departmentId: $this->ti->id, roomId: $this->ruangA1->id)
        ->assertSet('filterBuildingId', $this->gedungA->id)
        ->assertSet('filterDepartmentId', $this->ti->id)
        ->assertSet('filterRoomId', $this->ruangA1->id)
        ->set('search', 'AC')
        ->assertSee('AC Split 2 PK - AC-1')
        ->assertSee('AC Split 1,5 PK - AC-2')
        ->assertDontSee('AC Cassette 3 PK - AC-3');
});

it('searchable select aset membersihkan pilihan yang tidak cocok dengan filter', function () {
    $this->actingAs($this->teknisi);

    Livewire::test(SearchableSelect::class, [
        'type' => 'asset',
        'name' => 'asset_id',
        'label' => 'Aset',
    ])
        ->call('selectOption', $this->asset3->id)
        ->assertSet('selectedId', $this->asset3->id)
        ->dispatch('lokasi-filter-changed', buildingId: $this->gedungA->id, departmentId: $this->ti->id, roomId: $this->ruangA1->id)
        ->assertSet('selectedId', null)
        ->assertSet('selectedLabel', '')
        ->assertSet('search', '');
});
