<?php

namespace App\Modules\VendorProducts\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\VendorProducts\Models\VendorProduct;
use App\Modules\Products\Models\Product;
use App\Modules\Currencies\Models\Currency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = VendorProduct::with(['product', 'vendor', 'currency']);

        // Arama
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%");
            });
        }

        // Satıcı filtresi
        if ($request->has('user_id')) {
            $query->where('user_relation_id', $request->get('user_id'));
        }

        // Ürün filtresi
        if ($request->has('product_id')) {
            $query->where('relation_id', $request->get('product_id'));
        }

        // Durum filtresi
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Sıralama
        $query->orderBy('created_at', 'desc');

        $vendorProducts = $query->paginate(20);
        
        // Filtreleme için satıcılar
        $vendors = User::whereHas('role', function($q) {
            $q->where('name', 'Vendor');
        })->orderBy('first_name')->get();

        return view('vendor-products.index', compact('vendorProducts', 'vendors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::active()
            ->where('approval_status', 'approved')
            ->orderBy('name')
            ->get();
            
        $vendors = User::whereHas('role', function($q) {
            $q->where('name', 'Vendor');
        })->orderBy('first_name')->get();
        
        $currencies = Currency::active()->orderBy('name')->get();

        return view('vendor-products.create', compact('products', 'vendors', 'currencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'relation_id' => 'required|exists:products,id',
            'user_relation_id' => 'required|exists:users,id',
            'price' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'condition' => 'required|in:new,used,refurbished',
            'stock_quantity' => 'required|integer|min:0',
            'min_sale_quantity' => 'required|integer|min:1',
            'max_sale_quantity' => 'required|integer|min:1|gte:min_sale_quantity',
            'discount' => 'nullable|array',
            'discount.type' => 'nullable|required_with:discount|in:percentage,fixed',
            'discount.value' => 'nullable|required_with:discount|numeric|min:0',
            'discount.start_date' => 'nullable|required_with:discount|date',
            'discount.end_date' => 'nullable|required_with:discount|date|after:discount.start_date',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean',
        ]);

        // Aynı ürün ve satıcı kombinasyonunun var olup olmadığını kontrol et
        $exists = VendorProduct::where('relation_id', $validated['relation_id'])
            ->where('user_relation_id', $validated['user_relation_id'])
            ->exists();
            
        if ($exists) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Bu satıcı için bu ürün zaten tanımlanmış.');
        }

        DB::beginTransaction();
        try {
            $vendorProduct = VendorProduct::create($validated);

            // Resimleri yükle
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $vendorProduct->addMedia($image)
                        ->toMediaCollection('images');
                }
            }

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($vendorProduct)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $vendorProduct->toArray()])
                ->log('Müşteri ürünü oluşturuldu');

            DB::commit();

            return redirect()
                ->route('admin.vendor-products.index')
                ->with('success', 'Müşteri ürünü başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Müşteri ürünü oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(VendorProduct $vendorProduct)
    {
        $vendorProduct->load(['product', 'vendor', 'currency']);
        
        return view('vendor-products.show', compact('vendorProduct'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VendorProduct $vendorProduct)
    {
        $products = Product::active()
            ->where('approval_status', 'approved')
            ->orderBy('name')
            ->get();
            
        $vendors = User::whereHas('role', function($q) {
            $q->where('name', 'Vendor');
        })->orderBy('first_name')->get();
        
        $currencies = Currency::active()->orderBy('name')->get();

        return view('vendor-products.edit', compact('vendorProduct', 'products', 'vendors', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VendorProduct $vendorProduct)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'condition' => 'required|in:new,used,refurbished',
            'stock_quantity' => 'required|integer|min:0',
            'min_sale_quantity' => 'required|integer|min:1',
            'max_sale_quantity' => 'required|integer|min:1|gte:min_sale_quantity',
            'discount' => 'nullable|array',
            'discount.type' => 'nullable|required_with:discount|in:percentage,fixed',
            'discount.value' => 'nullable|required_with:discount|numeric|min:0',
            'discount.start_date' => 'nullable|required_with:discount|date',
            'discount.end_date' => 'nullable|required_with:discount|date|after:discount.start_date',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $oldAttributes = $vendorProduct->toArray();
            $vendorProduct->update($validated);

            // Yeni resimleri yükle
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $vendorProduct->addMedia($image)
                        ->toMediaCollection('images');
                }
            }

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($vendorProduct)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldAttributes,
                    'attributes' => $vendorProduct->toArray()
                ])
                ->log('Müşteri ürünü güncellendi');

            DB::commit();

            return redirect()
                ->route('admin.vendor-products.index')
                ->with('success', 'Müşteri ürünü başarıyla güncellendi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Müşteri ürünü güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VendorProduct $vendorProduct)
    {
        DB::beginTransaction();
        try {
            // Log aktiviteyi kaydet
            activity()
                ->performedOn($vendorProduct)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $vendorProduct->toArray()])
                ->log('Müşteri ürünü silindi');

            // Medya dosyalarını sil
            $vendorProduct->clearMediaCollection('images');
            
            $vendorProduct->delete();

            DB::commit();

            return redirect()
                ->route('admin.vendor-products.index')
                ->with('success', 'Müşteri ürünü başarıyla silindi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Müşteri ürünü silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Belirli bir satıcının ürünlerini listele
     */
    public function byVendor(User $vendor)
    {
        $vendorProducts = $vendor->vendorProducts()
            ->with(['product', 'currency'])
            ->paginate(20);

        return view('vendor-products.by-vendor', compact('vendor', 'vendorProducts'));
    }

    /**
     * Remove media from vendor product
     */
    public function removeMedia(VendorProduct $vendorProduct, $mediaId)
    {
        try {
            $vendorProduct->deleteMedia($mediaId);
            
            return response()->json([
                'success' => true,
                'message' => 'Resim başarıyla silindi.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Resim silinirken bir hata oluştu.'
            ], 500);
        }
    }
}
