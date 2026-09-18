<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-products') || $this->user()->hasAnyRole(['admin', 'super-admin']);
    }

    public function rules(): array
    {
        $product = $this->route('product');
        $productId = is_object($product) ? $product->id : $product;

        return [
            'category_id'   => ['required', 'integer', 'exists:categories,id'],
            'sku'           => ['nullable', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($productId)],
            'name'          => ['required', 'string', 'max:150'],
            'cost_price'    => ['required', 'integer', 'min:0'],
            'selling_price' => ['required', 'integer', 'min:0', 'gte:cost_price'],
            'stock'         => ['required', 'integer', 'min:0'],
            'image'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'image_url'     => ['nullable', 'url', 'max:500'],
            'is_active'     => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori menu wajib dipilih.',
            'category_id.exists'   => 'Kategori yang dipilih tidak valid.',
            'sku.unique'           => 'Kode SKU sudah terdaftar pada menu lain.',
            'name.required'        => 'Nama menu wajib diisi.',
            'cost_price.required'  => 'Modal dasar (HPP) wajib diisi.',
            'selling_price.gte'    => 'Harga jual tidak boleh lebih rendah dari modal dasar (HPP).',
            'stock.required'       => 'Jumlah stok menu wajib ditentukan.',
            'image.image'          => 'Berkas harus berupa gambar yang valid (jpeg, png, jpg, webp).',
            'image.max'            => 'Ukuran berkas gambar maksimal 2MB.',
            'image_url.url'        => 'Format tautan URL gambar tidak valid.',
        ];
    }
}