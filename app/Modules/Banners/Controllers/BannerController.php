<?php

namespace App\Modules\Banners\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Banners\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Banner::with('image');

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
        $query->orderBy('created_at', 'desc');

        $banners = $query->paginate(20);

        return view('banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('banners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link' => 'nullable|url',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Resmi yükle
            $imagePath = $request->file('image')->store('banners', 'public');
            
            // Upload modülüne kaydet
            $upload = \App\Modules\Uploads\Models\Upload::create([
                'name' => $request->file('image')->getClientOriginalName(),
                'type' => 'banner',
                'url' => asset('storage/' . $imagePath),
                'file_name' => basename($imagePath),
                'file_path' => $imagePath,
                'order' => 0,
                'status' => 1,
            ]);
            
            $validated['image_id'] = $upload->id;

            $banner = Banner::create($validated);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($banner)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $banner->toArray()])
                ->log('Banner oluşturuldu');

            DB::commit();

            return redirect()
                ->route('admin.banners.index')
                ->with('success', 'Banner başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Banner oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        $banner->load('image');
        
        return view('banners.show', compact('banner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        return view('banners.edit', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link' => 'nullable|url',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $oldAttributes = $banner->toArray();

            // Yeni resim yüklendi mi?
            if ($request->hasFile('image')) {
                // Eski resmi sil
                if ($banner->image_id && $banner->image) {
                    \Storage::disk('public')->delete($banner->image->file_path);
                    $banner->image->delete();
                }

                // Yeni resmi yükle
                $imagePath = $request->file('image')->store('banners', 'public');
                $upload = \App\Modules\Uploads\Models\Upload::create([
                    'name' => $request->file('image')->getClientOriginalName(),
                    'type' => 'banner',
                    'url' => asset('storage/' . $imagePath),
                    'file_name' => basename($imagePath),
                    'file_path' => $imagePath,
                    'order' => 0,
                    'status' => 1,
                ]);
                $validated['image_id'] = $upload->id;
            }

            $banner->update($validated);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($banner)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldAttributes,
                    'attributes' => $banner->toArray()
                ])
                ->log('Banner güncellendi');

            DB::commit();

            return redirect()
                ->route('admin.banners.index')
                ->with('success', 'Banner başarıyla güncellendi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Banner güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        DB::beginTransaction();
        try {
            // Resmi sil
            if ($banner->image_id && $banner->image) {
                \Storage::disk('public')->delete($banner->image->file_path);
                $banner->image->delete();
            }

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($banner)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $banner->toArray()])
                ->log('Banner silindi');

            $banner->delete();

            DB::commit();

            return redirect()
                ->route('admin.banners.index')
                ->with('success', 'Banner başarıyla silindi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Banner silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Toggle banner status via AJAX
     */
    public function toggleStatus(Banner $banner)
    {
        try {
            $banner->status = !$banner->status;
            $banner->save();

            return response()->json([
                'success' => true,
                'status' => $banner->status,
                'message' => 'Banner durumu güncellendi.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Durum güncellenirken bir hata oluştu.'
            ], 500);
        }
    }
}
