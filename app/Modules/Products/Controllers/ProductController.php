<?php

namespace App\Modules\Products\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Products\Models\Product;
use App\Modules\Categories\Models\Category;
use App\Modules\Brands\Models\Brand;
use App\Modules\Currencies\Models\Currency;
use App\Modules\ProductAttributes\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'defaultCurrency']);

        // Arama
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
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

        // Durum filtresi
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Onay durumu filtresi
        if ($request->has('approval_status')) {
            $query->where('approval_status', $request->get('approval_status'));
        }

        // Stok durumu filtresi
        if ($request->has('in_stock')) {
            if ($request->get('in_stock')) {
                $query->where('stock_quantity', '>', 0);
            } else {
                $query->where('stock_quantity', '=', 0);
            }
        }

        // Sıralama
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate(20);
        
        // Filtreler için veriler
        $categories = Category::active()->orderBy('name')->get();
        $brands = Brand::active()->orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        $brands = Brand::active()->orderBy('name')->get();
        $currencies = Currency::active()->get();
        $productAttributes = ProductAttribute::active()->with('attributeCategory')->get()->groupBy('attribute_category_id');

        return view('products.create', compact('categories', 'brands', 'currencies', 'productAttributes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_code' => 'required|string|max:255|unique:products',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'tags' => 'nullable|string',
            'barcode' => 'nullable|string|max:255|unique:products',
            'default_price' => 'required|numeric|min:0',
            'default_currency_id' => 'required|exists:currencies,id',
            'condition' => 'required|in:new,used,refurbished',
            'stock_quantity' => 'required|integer|min:0',
            'min_sale_quantity' => 'required|integer|min:1',
            'max_sale_quantity' => 'nullable|integer|min:1|gte:min_sale_quantity',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'attributes' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'exists:uploads,id',
            'status' => 'required|boolean',
        ]);

        // Tags'i array'e çevir
        if (isset($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        // Approval status'ü belirle (admin ise otomatik onaylı)
        $validated['approval_status'] = auth()->user()->hasRole('admin') ? 'approved' : 'pending';

        DB::beginTransaction();
        try {
            $product = Product::create($validated);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($product)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $product->toArray()])
                ->log('Ürün oluşturuldu');

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Ürün başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ürün oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'defaultCurrency', 'vendorProducts.vendor']);
        
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::active()->orderBy('name')->get();
        $brands = Brand::active()->orderBy('name')->get();
        $currencies = Currency::active()->get();
        $productAttributes = ProductAttribute::active()->with('attributeCategory')->get()->groupBy('attribute_category_id');

        return view('products.edit', compact('product', 'categories', 'brands', 'currencies', 'productAttributes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_code' => 'required|string|max:255|unique:products,product_code,' . $product->id,
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'tags' => 'nullable|string',
            'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $product->id,
            'default_price' => 'required|numeric|min:0',
            'default_currency_id' => 'required|exists:currencies,id',
            'condition' => 'required|in:new,used,refurbished',
            'stock_quantity' => 'required|integer|min:0',
            'min_sale_quantity' => 'required|integer|min:1',
            'max_sale_quantity' => 'nullable|integer|min:1|gte:min_sale_quantity',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'attributes' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'exists:uploads,id',
            'status' => 'required|boolean',
        ]);

        // Tags'i array'e çevir
        if (isset($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        DB::beginTransaction();
        try {
            $oldAttributes = $product->toArray();
            $product->update($validated);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($product)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldAttributes,
                    'attributes' => $product->toArray()
                ])
                ->log('Ürün güncellendi');

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Ürün başarıyla güncellendi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ürün güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Satıcı ürünleri kontrol et
        if ($product->vendorProducts()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Bu ürün satıcılar tarafından listelendiği için silinemez.');
        }

        DB::beginTransaction();
        try {
            // Log aktiviteyi kaydet
            activity()
                ->performedOn($product)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $product->toArray()])
                ->log('Ürün silindi');

            $product->delete();

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Ürün başarıyla silindi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Ürün silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Approve the product.
     */
    public function approve(Product $product)
    {
        if ($product->approval_status === 'approved') {
            return redirect()
                ->back()
                ->with('info', 'Bu ürün zaten onaylı.');
        }

        DB::beginTransaction();
        try {
            $product->approval_status = 'approved';
            $product->save();

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($product)
                ->causedBy(auth()->user())
                ->log('Ürün onaylandı');

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Ürün başarıyla onaylandı.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Ürün onaylanırken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Reject the product.
     */
    public function reject(Request $request, Product $product)
    {
        if ($product->approval_status === 'pending') {
            return redirect()
                ->back()
                ->with('info', 'Bu ürün zaten onay bekliyor.');
        }

        DB::beginTransaction();
        try {
            $product->approval_status = 'pending';
            $product->save();

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($product)
                ->causedBy(auth()->user())
                ->withProperties(['reason' => $request->get('reason')])
                ->log('Ürün onayı reddedildi');

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Ürün onayı reddedildi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'İşlem sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Update stock via AJAX.
     */
    public function updateStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
            'operation' => 'required|in:set,increment,decrement',
        ]);

        try {
            $product->updateStock($validated['quantity'], $validated['operation']);

            return response()->json([
                'success' => true,
                'message' => 'Stok güncellendi.',
                'new_quantity' => $product->stock_quantity
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stok güncellenirken bir hata oluştu.'
            ], 500);
        }
    }
}
