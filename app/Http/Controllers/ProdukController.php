<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    /**
     * Tampilkan halaman produk untuk pengunjung/customer
     */
    public function index()
    {
        // Ambil produk yang aktif saja
        $products = Product::where('is_active', 1)
                          ->where('stock', '>', 0) // hanya yang ada stoknya
                          ->latest()
                          ->get();
        
        return view('produk', compact('products'));
    }

    /**
     * Tampilkan detail produk
     */
    public function show($id)
    {
        $product = Product::where('is_active', 1)->findOrFail($id);
        return view('produk.detail', compact('product'));
    }

    /**
     * API untuk cart (jika diperlukan)
     */
    public function getProduct($id)
    {
        $product = Product::where('is_active', 1)->find($id);
        
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'image' => asset('storage/' . $product->image),
            'stock' => $product->stock
        ]);
    }
}