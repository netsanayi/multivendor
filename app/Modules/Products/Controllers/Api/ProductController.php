<?php

namespace App\Modules\Products\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Products\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'defaultCurrency'])
            ->active()
            ->approved();

        // Arama
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Kategori filtresi
        if ($request->has('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        // Marka filtresi
        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->get('brand_id'));
        }

        // Fiyat aralığı
        if ($request->has('min_price')) {
            $query->where('default_price', '>=', $request->get('min_price'));
        }

        if ($request->has('max_price')) {
            $query->where('default_price', '<=', $request->get('max_price'));
        }

        // Sıralama
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        if (in_array($sortBy, ['name', 'default_price', 'created_at', 'stock_quantity'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = min($request->get('per_page', 20), 100); // Maximum 100 per page
        $products = $query->paginate($perPage);

        return response()->json($products);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Only show active and approved products
        if (!$product->isActive() || !$product->isApproved()) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->load(['category', 'brand', 'defaultCurrency', 'vendorProducts.vendor']);
        
        return response()->json($product);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
