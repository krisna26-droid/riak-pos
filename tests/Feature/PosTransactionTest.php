<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PosTransactionTest extends TestCase
{
    use RefreshDatabase;

    protected User $cashier;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup role dan izin
        $cashierRole = Role::create(['name' => 'cashier']);
        $permission = Permission::create(['name' => 'process-pos']);
        $cashierRole->givePermissionTo($permission);

        $this->cashier = User::factory()->create();
        $this->cashier->assignRole('cashier');

        // Setup master produk
        $category = Category::create(['name' => 'Coffee', 'slug' => 'coffee']);
        $this->product = Product::create([
            'category_id'   => $category->id,
            'sku'           => 'RC-TST-01',
            'name'          => 'Iced Latte Test',
            'cost_price'    => 10000,
            'selling_price' => 25000,
            'stock'         => 10,
            'is_active'     => true,
        ]);
    }

    public function test_transaksi_gagal_jika_shift_belum_dibuka(): void
    {
        $payload = [
            'payment_method' => 'cash',
            'cash_received'  => 50000,
            'items' => [
                ['id' => $this->product->id, 'quantity' => 1],
            ],
        ];

        $response = $this->actingAs($this->cashier)->postJson('/pos/checkout', $payload);

        $response->assertStatus(422)
                 ->assertJsonFragment(['message' => 'Shift kasir belum dibuka atau sudah ditutup.']);
    }

    public function test_siklus_buka_shift_transaksi_dan_tutup_shift(): void
    {
        // 1. Buka shift kasir dengan modal awal Rp 100.000
        $openShiftResponse = $this->actingAs($this->cashier)->postJson('/pos/shift/open', [
            'starting_cash' => 100000,
        ]);
        $openShiftResponse->assertStatus(201);

        // 2. Checkout 2 porsi Latte (Total harga: 2 * 25.000 = 50.000, PPN 11% = 5.500 -> Grand Total = 55.500)
        $checkoutPayload = [
            'payment_method' => 'cash',
            'cash_received'  => 60000,
            'discount'       => 0,
            'items' => [
                ['id' => $this->product->id, 'quantity' => 2],
            ],
        ];

        $checkoutResponse = $this->actingAs($this->cashier)->postJson('/pos/checkout', $checkoutPayload);

        $checkoutResponse->assertStatus(201)
                         ->assertJsonPath('order.grand_total', 55500)
                         ->assertJsonPath('order.cash_change', 4500);

        // Pastikan stok berkurang di database dari 10 menjadi 8
        $this->assertDatabaseHas('products', [
            'id'    => $this->product->id,
            'stock' => 8,
        ]);

        // 3. Tutup Shift Kasir
        // Expected ending cash: Modal awal (100.000) + Grand Total Cash (55.500) = 155.500
        // Kasir menghitung fisik uang dan memasukkan 155.500 (selisih harus 0)
        $closeShiftResponse = $this->actingAs($this->cashier)->postJson('/pos/shift/close', [
            'actual_ending_cash' => 155500,
        ]);

        $closeShiftResponse->assertStatus(200)
                           ->assertJsonPath('shift.expected_ending_cash', 155500)
                           ->assertJsonPath('shift.difference', 0);
    }

    public function test_transaksi_ditolak_jika_stok_tidak_mencukupi(): void
    {
        // Buka shift
        $this->actingAs($this->cashier)->postJson('/pos/shift/open', ['starting_cash' => 50000]);

        // Beli melebihi stok yang ada (stok ada 10, minta beli 15)
        $payload = [
            'payment_method' => 'cash',
            'cash_received'  => 500000,
            'items' => [
                ['id' => $this->product->id, 'quantity' => 15],
            ],
        ];

        $response = $this->actingAs($this->cashier)->postJson('/pos/checkout', $payload);

        $response->assertStatus(422);

        // Stok awal tidak boleh berubah
        $this->assertDatabaseHas('products', [
            'id'    => $this->product->id,
            'stock' => 10,
        ]);
    }
}