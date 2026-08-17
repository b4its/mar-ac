<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest diarahkan ke halaman login', function () {
    $response = $this->get('/');

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('admin diarahkan ke beranda fitur dan melihat akses panel admin', function () {
    $this->seed(RolePermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->get('/')
        ->assertRedirect(route('welcome'));

    $this->actingAs($admin)
        ->get(route('welcome'))
        ->assertOk()
        ->assertSee('Panel Admin')
        ->assertSee(route('filament.admin.pages.dashboard'));
});
