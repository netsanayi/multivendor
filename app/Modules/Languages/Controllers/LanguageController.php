<?php

namespace App\Modules\Languages\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Languages\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Language::with('image');

        // Arama
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }

        // Durum filtresi
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Sıralama
        $query->orderBy('order', 'asc')->orderBy('name', 'asc');

        $languages = $query->paginate(20);

        return view('languages.index', compact('languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('languages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:languages,code',
            'name' => 'required|string|max:255',
            'locale' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Resmi yükle (bayrak)
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('languages', 'public');
                // Upload modülüne kaydet
                $upload = \App\Modules\Uploads\Models\Upload::create([
                    'name' => $request->file('image')->getClientOriginalName(),
                    'type' => 'language',
                    'url' => asset('storage/' . $imagePath),
                    'file_name' => basename($imagePath),
                    'file_path' => $imagePath,
                    'order' => 0,
                    'status' => 1,
                ]);
                $validated['image_id'] = $upload->id;
            }

            $language = Language::create($validated);

            // Dil dosyalarını oluştur
            $this->createLanguageFiles($language->code);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($language)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $language->toArray()])
                ->log('Dil oluşturuldu');

            DB::commit();

            return redirect()
                ->route('admin.languages.index')
                ->with('success', 'Dil başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Dil oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Language $language)
    {
        $language->load('image');
        
        return view('languages.show', compact('language'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Language $language)
    {
        return view('languages.edit', compact('language'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Language $language)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:languages,code,' . $language->id,
            'name' => 'required|string|max:255',
            'locale' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $oldAttributes = $language->toArray();
            $oldCode = $language->code;

            // Yeni resim yüklendi mi?
            if ($request->hasFile('image')) {
                // Eski resmi sil
                if ($language->image_id && $language->image) {
                    \Storage::disk('public')->delete($language->image->file_path);
                    $language->image->delete();
                }

                // Yeni resmi yükle
                $imagePath = $request->file('image')->store('languages', 'public');
                $upload = \App\Modules\Uploads\Models\Upload::create([
                    'name' => $request->file('image')->getClientOriginalName(),
                    'type' => 'language',
                    'url' => asset('storage/' . $imagePath),
                    'file_name' => basename($imagePath),
                    'file_path' => $imagePath,
                    'order' => 0,
                    'status' => 1,
                ]);
                $validated['image_id'] = $upload->id;
            }

            $language->update($validated);

            // Dil kodu değiştiyse dosyaları yeniden adlandır
            if ($oldCode !== $validated['code']) {
                $this->renameLanguageFiles($oldCode, $validated['code']);
            }

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($language)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldAttributes,
                    'attributes' => $language->toArray()
                ])
                ->log('Dil güncellendi');

            DB::commit();

            return redirect()
                ->route('admin.languages.index')
                ->with('success', 'Dil başarıyla güncellendi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Dil güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language)
    {
        // Varsayılan dil kontrolü
        if ($language->code === 'tr') {
            return redirect()
                ->back()
                ->with('error', 'Varsayılan dil silinemez.');
        }

        DB::beginTransaction();
        try {
            // Resmi sil
            if ($language->image_id && $language->image) {
                \Storage::disk('public')->delete($language->image->file_path);
                $language->image->delete();
            }

            // Dil dosyalarını sil
            $this->deleteLanguageFiles($language->code);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($language)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $language->toArray()])
                ->log('Dil silindi');

            $language->delete();

            DB::commit();

            return redirect()
                ->route('admin.languages.index')
                ->with('success', 'Dil başarıyla silindi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Dil silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Update language order via AJAX
     */
    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'languages' => 'required|array',
            'languages.*.id' => 'required|exists:languages,id',
            'languages.*.order' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['languages'] as $item) {
                Language::where('id', $item['id'])
                    ->update(['order' => $item['order']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dil sıralaması güncellendi.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Sıralama güncellenirken bir hata oluştu.'
            ], 500);
        }
    }

    /**
     * Create language files
     */
    private function createLanguageFiles($code)
    {
        $langPath = resource_path('lang/' . $code);
        
        if (!file_exists($langPath)) {
            mkdir($langPath, 0755, true);
        }

        // Varsayılan dosyaları kopyala
        $defaultPath = resource_path('lang/tr');
        if (file_exists($defaultPath)) {
            $files = glob($defaultPath . '/*.php');
            foreach ($files as $file) {
                $filename = basename($file);
                if (!file_exists($langPath . '/' . $filename)) {
                    copy($file, $langPath . '/' . $filename);
                }
            }
        }
    }

    /**
     * Rename language files
     */
    private function renameLanguageFiles($oldCode, $newCode)
    {
        $oldPath = resource_path('lang/' . $oldCode);
        $newPath = resource_path('lang/' . $newCode);
        
        if (file_exists($oldPath) && !file_exists($newPath)) {
            rename($oldPath, $newPath);
        }
    }

    /**
     * Delete language files
     */
    private function deleteLanguageFiles($code)
    {
        $langPath = resource_path('lang/' . $code);
        
        if (file_exists($langPath)) {
            // Dizindeki tüm dosyaları sil
            $files = glob($langPath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            // Dizini sil
            rmdir($langPath);
        }
    }
}
