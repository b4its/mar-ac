<?php

use App\Livewire\SearchableSelect;
use App\Models\Asset;
use App\Models\Building;
use App\Models\Department;
use App\Models\Room;
use App\Models\User;
use App\Models\Vendor;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);

    $this->user = User::factory()->create();
    $this->user->assignRole('teknisi');
});

it('searchable select dapat mencari dan memilih aset', function () {
    $asset = Asset::create([
        'nama_alat' => 'Pompa Air Utama',
        'kode_alat' => 'PMP-001',
        'status' => 'baik',
    ]);

    Livewire::actingAs($this->user)
        ->test(SearchableSelect::class, [
            'type' => 'asset',
            'name' => 'asset_id',
            'label' => 'Aset',
            'required' => true,
        ])
        ->set('search', 'Pompa')
        ->assertSee('Pompa Air Utama - PMP-001')
        ->call('selectOption', $asset->id)
        ->assertSet('selectedId', $asset->id)
        ->assertSet('search', 'Pompa Air Utama - PMP-001');
});

it('searchable select dapat menambah aset baru lalu otomatis memilihnya', function () {
    $building = Building::create(['nama_gedung' => 'Gedung C', 'kode_gedung' => 'C']);
    $room = Room::create(['building_id' => $building->id, 'nama_ruangan' => 'Lab Komputer 1', 'kode_ruangan' => 'C-101']);
    $department = Department::create(['nama_jurusan' => 'Akuntansi', 'kode_jurusan' => 'AK']);

    Livewire::actingAs($this->user)
        ->test(SearchableSelect::class, [
            'type' => 'asset',
            'name' => 'asset_id',
            'label' => 'Aset',
        ])
        ->set('search', 'Generator Baru')
        ->call('startCreate')
        ->set('newName', 'Generator Baru')
        ->set('newAssetType', 'Genset')
        ->set('newCode', 'GEN-001')
        ->set('newInventory', 'INV-GEN-001')
        ->set('newRoomId', (string) $room->id)
        ->set('newDepartmentId', (string) $department->id)
        ->set('newCapacity', '5000 Watt')
        ->set('newBrand', 'Honda')
        ->set('newYear', '2024')
        ->set('newStatus', 'rusak_ringan')
        ->set('newLastMaintenanceDate', now()->toDateString())
        ->set('newDescription', 'Aset dibuat dari form laporan')
        ->call('createOption')
        ->assertSet('selectedId', Asset::first()->id)
        ->assertDispatched('toast');

    expect(Asset::first())
        ->nama_alat->toBe('Generator Baru')
        ->jenis_alat->toBe('Genset')
        ->kode_alat->toBe('GEN-001')
        ->no_inventaris->toBe('INV-GEN-001')
        ->room_id->toBe($room->id)
        ->department_id->toBe($department->id)
        ->kapasitas->toBe('5000 Watt')
        ->merk->toBe('Honda')
        ->tahun_pemakaian->toBe('2024')
        ->status->toBe('rusak_ringan')
        ->last_maintenance_date->toDateString()->toBe(now()->toDateString())
        ->keterangan->toBe('Aset dibuat dari form laporan');
});

it('searchable select dapat menambah vendor baru lalu otomatis memilihnya', function () {
    Livewire::actingAs($this->user)
        ->test(SearchableSelect::class, [
            'type' => 'vendor',
            'name' => 'vendor_id',
            'label' => 'Vendor',
        ])
        ->set('search', 'PT Teknologi Baru')
        ->call('startCreate')
        ->set('newName', 'PT Teknologi Baru')
        ->set('newContact', 'Budi')
        ->set('newPhone', '08123456789')
        ->set('newAddress', 'Jl. Cipto Mangunkusumo')
        ->set('newVendorDescription', 'Vendor dari form laporan')
        ->call('createOption')
        ->assertSet('selectedId', Vendor::first()->id)
        ->assertDispatched('toast');

    expect(Vendor::first())
        ->nama_vendor->toBe('PT Teknologi Baru')
        ->kontak->toBe('Budi')
        ->telepon->toBe('08123456789')
        ->alamat->toBe('Jl. Cipto Mangunkusumo')
        ->keterangan->toBe('Vendor dari form laporan');
});
