<?php

namespace App\Modules\Blogs\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Blogs\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Blog::query();

        // Arama
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Durum filtresi
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Sıralama
        $query->orderBy('created_at', 'desc');

        $blogs = $query->paginate(20);

        return view('blogs.index', compact('blogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('blogs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        // Slug oluştur
        $validated['slug'] = Str::slug($validated['title']);
        
        // Slug benzersizliğini kontrol et
        $originalSlug = $validated['slug'];
        $count = 1;
        while (Blog::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count;
            $count++;
        }

        DB::beginTransaction();
        try {
            $blog = Blog::create($validated);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($blog)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $blog->toArray()])
                ->log('Blog yazısı oluşturuldu');

            DB::commit();

            return redirect()
                ->route('admin.blogs.index')
                ->with('success', 'Blog yazısı başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Blog yazısı oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        return view('blogs.show', compact('blog'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blog $blog)
    {
        return view('blogs.edit', compact('blog'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blog $blog)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $oldAttributes = $blog->toArray();
            $blog->update($validated);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($blog)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldAttributes,
                    'attributes' => $blog->toArray()
                ])
                ->log('Blog yazısı güncellendi');

            DB::commit();

            return redirect()
                ->route('admin.blogs.index')
                ->with('success', 'Blog yazısı başarıyla güncellendi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Blog yazısı güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        DB::beginTransaction();
        try {
            // Log aktiviteyi kaydet
            activity()
                ->performedOn($blog)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $blog->toArray()])
                ->log('Blog yazısı silindi');

            $blog->delete();

            DB::commit();

            return redirect()
                ->route('admin.blogs.index')
                ->with('success', 'Blog yazısı başarıyla silindi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Blog yazısı silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }
}
