<?php

namespace App\Modules\AttributeCategories\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AttributeCategories\Models\AttributeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttributeCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AttributeCategory::with('image');

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
        $query->orderBy('name', 'asc');

        $attributeCategories = $query->paginate(20);

        return view('attribute-categories.index', compact('attributeCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('attribute-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:attribute_categories,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Resmi yükle
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('attribute-categories', 'public');
                // Upload modülüne kaydet ve ID'yi al
                $upload = \App\Modules\Uploads\Models\Upload::create([
                    'name' => $request->file('image')->getClientOriginalName(),
                    'type' => 'attribute_category',
                    'url' => asset('storage/' . $imagePath),
                    'file_name' => basename($imagePath),
                    'file_path' => $imagePath,
                    'order' => 0,
                    'status' => 1,
                ]);
                $validated['image_id'] = $upload->id;
            }

            $attributeCategory = AttributeCategory::create($validated);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($attributeCategory)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $attributeCategory->toArray()])
                ->log('Özellik kategorisi oluşturuldu');

            DB::commit();

            return redirect()
                ->route('admin.attribute-categories.index')
                ->with('success', 'Özellik kategorisi başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Özellik kategorisi oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AttributeCategory $attributeCategory)
    {
        $attributeCategory->load(['image', 'productAttributes']);
        
        return view('attribute-categories.show', compact('attributeCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttributeCategory $attributeCategory)
    {
        return view('attribute-categories.edit', compact('attributeCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AttributeCategory $attributeCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:attribute_categories,name,' . $attributeCategory->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $oldAttributes = $attributeCategory->toArray();

            // Yeni resim yüklendi mi?
            if ($request->hasFile('image')) {
                // Eski resmi sil
                if ($attributeCategory->image_id && $attributeCategory->image) {
                    \Storage::disk('public')->delete($attributeCategory->image->file_path);
                    $attributeCategory->image->delete();
                }

                // Yeni resmi yükle
                $imagePath = $request->file('image')->store('attribute-categories', 'public');
                $upload = \App\Modules\Uploads\Models\Upload::create([
                    'name' => $request->file('image')->getClientOriginalName(),
                    'type' => 'attribute_category',
                    'url' => asset('storage/' . $imagePath),
                    'file_name' => basename($imagePath),
                    'file_path' => $imagePath,
                    'order' => 0,
                    'status' => 1,
                ]);
                $validated['image_id'] = $upload->id;
            }

            $attributeCategory->update($validated);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($attributeCategory)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldAttributes,
                    'attributes' => $attributeCategory->toArray()
                ])
                ->log('Özellik kategorisi güncellendi');

            DB::commit();

            return redirect()
                ->route('admin.attribute-categories.index')
                ->with('success', 'Özellik kategorisi başarıyla güncellendi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Özellik kategorisi güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttributeCategory $attributeCategory)
    {
        // İlişkili özellikler var mı kontrol et
        if ($attributeCategory->productAttributes()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Özellikleri olan bir kategori silinemez.');
        }

        DB::beginTransaction();
        try {
            // Resmi sil
            if ($attributeCategory->image_id && $attributeCategory->image) {
                \Storage::disk('public')->delete($attributeCategory->image->file_path);
                $attributeCategory->image->delete();
            }

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($attributeCategory)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $attributeCategory->toArray()])
                ->log('Özellik kategorisi silindi');

            $attributeCategory->delete();

            DB::commit();

            return redirect()
                ->route('admin.attribute-categories.index')
                ->with('success', 'Özellik kategorisi başarıyla silindi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Özellik kategorisi silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }
}
