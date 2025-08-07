<?php

namespace App\Modules\Categories\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Categories\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::with(['parent', 'image']);

        // Arama
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Durum filtresi
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Ana kategori filtresi
        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->get('parent_id'));
        }

        // Sıralama
        $query->orderBy('order', 'asc')->orderBy('name', 'asc');

        $categories = $query->paginate(20);

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentCategories = Category::active()
            ->orderBy('name')
            ->get();

        return view('categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'image_id' => 'nullable|exists:uploads,id',
            'column_count' => 'required|integer|min:1|max:6',
            'order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $category = Category::create($validated);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($category)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $category->toArray()])
                ->log('Kategori oluşturuldu');

            DB::commit();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Kategori başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Kategori oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load(['parent', 'children', 'image', 'products']);
        
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $parentCategories = Category::active()
            ->where('id', '!=', $category->id)
            ->whereNotIn('id', $category->descendants->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:categories,id|not_in:' . $category->id,
            'image_id' => 'nullable|exists:uploads,id',
            'column_count' => 'required|integer|min:1|max:6',
            'order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        // Alt kategorilerin ID'lerini kontrol et
        $descendantIds = $category->descendants->pluck('id')->toArray();
        if (in_array($validated['parent_id'] ?? null, $descendantIds)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Bir kategori kendi alt kategorisi olamaz.');
        }

        DB::beginTransaction();
        try {
            $oldAttributes = $category->toArray();
            $category->update($validated);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($category)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldAttributes,
                    'attributes' => $category->toArray()
                ])
                ->log('Kategori güncellendi');

            DB::commit();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Kategori başarıyla güncellendi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Kategori güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Alt kategorileri kontrol et
        if ($category->hasChildren()) {
            return redirect()
                ->back()
                ->with('error', 'Alt kategorileri olan bir kategori silinemez.');
        }

        // Ürünleri kontrol et
        if ($category->products()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Ürünleri olan bir kategori silinemez.');
        }

        DB::beginTransaction();
        try {
            // Log aktiviteyi kaydet
            activity()
                ->performedOn($category)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $category->toArray()])
                ->log('Kategori silindi');

            $category->delete();

            DB::commit();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Kategori başarıyla silindi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Kategori silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Update category order via AJAX.
     */
    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.order' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['categories'] as $item) {
                Category::where('id', $item['id'])
                    ->update(['order' => $item['order']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kategori sıralaması güncellendi.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Sıralama güncellenirken bir hata oluştu.'
            ], 500);
        }
    }
}
