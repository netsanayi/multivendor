<?php

namespace App\Modules\ProductAttributes\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ProductAttributes\Models\ProductAttribute;
use App\Modules\AttributeCategories\Models\AttributeCategory;
use App\Modules\Categories\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductAttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProductAttribute::with(['attributeCategory', 'image']);

        // Arama
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Özellik kategorisi filtresi
        if ($request->has('attribute_category_id')) {
            $query->where('attribute_category_id', $request->get('attribute_category_id'));
        }

        // Durum filtresi
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Sıralama
        $query->orderBy('order', 'asc')->orderBy('name', 'asc');

        $attributes = $query->paginate(20);
        
        // Filtreleme için özellik kategorileri
        $attributeCategories = AttributeCategory::active()->orderBy('name')->get();

        return view('product-attributes.index', compact('attributes', 'attributeCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $attributeCategories = AttributeCategory::active()->orderBy('name')->get();
        $productCategories = Category::active()->orderBy('name')->get();

        return view('product-attributes.create', compact('attributeCategories', 'productCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attribute_category_id' => 'required|exists:attribute_categories,id',
            'product_category_ids' => 'nullable|array',
            'product_category_ids.*' => 'exists:categories,id',
            'order' => 'required|integer|min:0',
            'values' => 'required|array|min:1',
            'values.*' => 'required|string|distinct',
            'status' => 'required|boolean',
        ]);

        // Değerleri JSON formatına dönüştür
        $validated['values'] = array_values(array_filter($validated['values']));

        DB::beginTransaction();
        try {
            // Resmi yükle
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('product-attributes', 'public');
                // Upload modülüne kaydet ve ID'yi al
                $upload = \App\Modules\Uploads\Models\Upload::create([
                    'name' => $request->file('image')->getClientOriginalName(),
                    'type' => 'product_attribute',
                    'url' => asset('storage/' . $imagePath),
                    'file_name' => basename($imagePath),
                    'file_path' => $imagePath,
                    'order' => 0,
                    'status' => 1,
                ]);
                $validated['image_id'] = $upload->id;
            }

            $attribute = ProductAttribute::create($validated);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($attribute)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $attribute->toArray()])
                ->log('Ürün özelliği oluşturuldu');

            DB::commit();

            return redirect()
                ->route('admin.product-attributes.index')
                ->with('success', 'Ürün özelliği başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ürün özelliği oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductAttribute $productAttribute)
    {
        $productAttribute->load(['attributeCategory', 'image']);
        
        // İlişkili ürün kategorilerini getir
        $relatedCategories = [];
        if ($productAttribute->product_category_ids) {
            $relatedCategories = Category::whereIn('id', $productAttribute->product_category_ids)->get();
        }
        
        return view('product-attributes.show', compact('productAttribute', 'relatedCategories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductAttribute $productAttribute)
    {
        $attributeCategories = AttributeCategory::active()->orderBy('name')->get();
        $productCategories = Category::active()->orderBy('name')->get();

        return view('product-attributes.edit', compact('productAttribute', 'attributeCategories', 'productCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductAttribute $productAttribute)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attribute_category_id' => 'required|exists:attribute_categories,id',
            'product_category_ids' => 'nullable|array',
            'product_category_ids.*' => 'exists:categories,id',
            'order' => 'required|integer|min:0',
            'values' => 'required|array|min:1',
            'values.*' => 'required|string|distinct',
            'status' => 'required|boolean',
        ]);

        // Değerleri JSON formatına dönüştür
        $validated['values'] = array_values(array_filter($validated['values']));

        DB::beginTransaction();
        try {
            $oldAttributes = $productAttribute->toArray();

            // Yeni resim yüklendi mi?
            if ($request->hasFile('image')) {
                // Eski resmi sil
                if ($productAttribute->image_id && $productAttribute->image) {
                    \Storage::disk('public')->delete($productAttribute->image->file_path);
                    $productAttribute->image->delete();
                }

                // Yeni resmi yükle
                $imagePath = $request->file('image')->store('product-attributes', 'public');
                $upload = \App\Modules\Uploads\Models\Upload::create([
                    'name' => $request->file('image')->getClientOriginalName(),
                    'type' => 'product_attribute',
                    'url' => asset('storage/' . $imagePath),
                    'file_name' => basename($imagePath),
                    'file_path' => $imagePath,
                    'order' => 0,
                    'status' => 1,
                ]);
                $validated['image_id'] = $upload->id;
            }

            $productAttribute->update($validated);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($productAttribute)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldAttributes,
                    'attributes' => $productAttribute->toArray()
                ])
                ->log('Ürün özelliği güncellendi');

            DB::commit();

            return redirect()
                ->route('admin.product-attributes.index')
                ->with('success', 'Ürün özelliği başarıyla güncellendi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ürün özelliği güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductAttribute $productAttribute)
    {
        // Ürünlerde kullanılıyor mu kontrol et
        // Bu kontrol, products tablosundaki attributes JSON alanında yapılmalı
        $productsUsingAttribute = \App\Modules\Products\Models\Product::where('attributes', 'like', '%"' . $productAttribute->id . '"%')->exists();
        
        if ($productsUsingAttribute) {
            return redirect()
                ->back()
                ->with('error', 'Ürünlerde kullanılan bir özellik silinemez.');
        }

        DB::beginTransaction();
        try {
            // Resmi sil
            if ($productAttribute->image_id && $productAttribute->image) {
                \Storage::disk('public')->delete($productAttribute->image->file_path);
                $productAttribute->image->delete();
            }

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($productAttribute)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $productAttribute->toArray()])
                ->log('Ürün özelliği silindi');

            $productAttribute->delete();

            DB::commit();

            return redirect()
                ->route('admin.product-attributes.index')
                ->with('success', 'Ürün özelliği başarıyla silindi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Ürün özelliği silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Belirli bir kategorinin özelliklerini getir (AJAX)
     */
    public function getByCategoryId($categoryId)
    {
        $attributes = ProductAttribute::active()
            ->where(function($query) use ($categoryId) {
                $query->whereJsonContains('product_category_ids', $categoryId)
                    ->orWhereNull('product_category_ids')
                    ->orWhere('product_category_ids', '[]');
            })
            ->with('attributeCategory')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return response()->json($attributes);
    }
}
