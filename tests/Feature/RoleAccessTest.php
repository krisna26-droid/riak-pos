<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan role
        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'cashier']);
    }

    public function test_kasir_tidak_dapat_mengakses_manajemen_user(): void
    {
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');

        $response = $this->actingAs($cashier)->getJson('/super-admin/users');

        $response->assertStatus(403);
    }

    public function test_kasir_tidak_dapat_mengakses_crud_produk(): void
    {
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');

        $response = $this->actingAs($cashier)->getJson('/admin/products');

        $response->assertStatus(403);
    }

    public function test_super_admin_memiliki_akses_ke_manajemen_user(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $response = $this->actingAs($superAdmin)->getJson('/super-admin/users');

        $response->assertStatus(200);
    }
}