<?php

namespace App\Modules\Brands\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Brands\Models\Brand;
use App\Modules\Categories\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Brand::with('image');

        // Arama
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Durum filtresi
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Sıralama
        $query->orderBy('order', 'asc')->orderBy('name', 'asc');

        $brands = $query->paginate(20);

        return view('brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::active()
            ->orderBy('name')
            ->get();

        return view('brands.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'image_id' => 'nullable|exists:uploads,id',
            'order' => 'required|integer|min:0',
            'product_category_ids' => 'nullable|array',
            'product_category_ids.*' => 'exists:categories,id',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $brand = Brand::create($validated);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($brand)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $brand->toArray()])
                ->log('Marka oluşturuldu');

            DB::commit();

            return redirect()
                ->route('admin.brands.index')
                ->with('success', 'Marka başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Marka oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        $brand->load(['image', 'products']);
        
        return view('brands.show', compact('brand'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        $categories = Category::active()
            ->orderBy('name')
            ->get();

        return view('brands.edit', compact('brand', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'image_id' => 'nullable|exists:uploads,id',
            'order' => 'required|integer|min:0',
            'product_category_ids' => 'nullable|array',
            'product_category_ids.*' => 'exists:categories,id',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $oldAttributes = $brand->toArray();
            $brand->update($validated);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($brand)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldAttributes,
                    'attributes' => $brand->toArray()
                ])
                ->log('Marka güncellendi');

            DB::commit();

            return redirect()
                ->route('admin.brands.index')
                ->with('success', 'Marka başarıyla güncellendi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Marka güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        // Ürünleri kontrol et
        if ($brand->products()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Ürünleri olan bir marka silinemez.');
        }

        DB::beginTransaction();
        try {
            // Log aktiviteyi kaydet
            activity()
                ->performedOn($brand)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $brand->toArray()])
                ->log('Marka silindi');

            $brand->delete();

            DB::commit();

            return redirect()
                ->route('admin.brands.index')
                ->with('success', 'Marka başarıyla silindi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Marka silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }
}
