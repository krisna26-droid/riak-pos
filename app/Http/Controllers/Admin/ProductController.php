<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Menampilkan tabel daftar seluruh produk menu dan stoknya.
     */
    public function index(Request $request): View
    {
        $categories = Category::orderBy('name')->get();

        $products = Product::with('category')
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('sku', 'like', '%' . $request->search . '%');
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Menampilkan form untuk menambah produk menu baru.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Menyimpan produk menu baru ke basis data beserta unggahan gambar.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (empty($validated['sku'])) {
            $validated['sku'] = $this->generateUniqueSku();
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        // Penanganan Unggahan Berkas Gambar atau Alternatif URL Eksternal
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = Storage::url($path);
        } elseif ($request->filled('image_url')) {
            $validated['image'] = $request->image_url;
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Menu baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit produk menu.
     */
    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Memperbarui data produk menu di basis data.
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        $validated['is_active'] = $request->boolean('is_active');

        // Jika ada unggahan gambar baru, hapus gambar lama dari storage lokal publik jika ada
        if ($request->hasFile('image')) {
            if ($product->image && str_contains($product->image, '/storage/products/')) {
                $oldPath = str_replace('/storage/', '', $product->image);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = Storage::url($path);
        } elseif ($request->filled('image_url')) {
            $validated['image'] = $request->image_url;
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    /**
     * Menghapus produk menu dari basis data.
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->orderItems()->exists()) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Menu tidak dapat dihapus karena sudah memiliki riwayat transaksi.');
        }

        if ($product->image && str_contains($product->image, '/storage/products/')) {
            $oldPath = str_replace('/storage/', '', $product->image);
            Storage::disk('public')->delete($oldPath);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Menu berhasil dihapus.');
    }

    /**
     * Menambah stok produk secara instan dari tabel inventaris.
     */
    public function quickRestock(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'additional_stock' => ['required', 'integer', 'min:1'],
        ]);

        $product->increment('stock', $request->additional_stock);

        return redirect()->route('admin.products.index')
            ->with('success', "Stok {$product->name} berhasil ditambah sebanyak {$request->additional_stock}.");
    }

    /**
     * Membuat SKU unik otomatis dengan format RC-XXXXXX
     */
    private function generateUniqueSku(): string
    {
        do {
            $sku = 'RC-' . strtoupper(Str::random(6));
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }
}