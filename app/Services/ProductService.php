<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ProductService
{
    public function createProduct(array $data, ?UploadedFile $image = null): Product
    {
        if ($image) {
            $data['image'] = $image->store('products', 'public');
        }
        return Product::create($data);
    }

    public function updateProduct(int $id, array $data, ?UploadedFile $image = null): bool
    {
        $product = Product::findOrFail($id);
        
        if ($image) {
            // Hapus gambar lama jika ada
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $image->store('products', 'public');
        }

        return $product->update($data);
    }

    public function deleteProduct(int $id): bool
    {
        $product = Product::findOrFail($id);
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        return $product->delete();
    }
}