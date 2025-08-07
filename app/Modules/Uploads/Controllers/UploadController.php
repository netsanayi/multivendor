<?php

namespace App\Modules\Uploads\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Uploads\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * Upload image via AJAX
     */
    public function uploadImage(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'nullable|string',
        ]);

        try {
            $file = $request->file('file');
            $type = $request->get('type', 'general');
            
            // Dosya yolunu belirle
            $path = 'uploads/' . $type . '/' . date('Y/m');
            
            // Dosyayı yükle
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs($path, $filename, 'public');
            
            // Upload kaydını oluştur
            $upload = Upload::create([
                'name' => $file->getClientOriginalName(),
                'type' => $type,
                'url' => asset('storage/' . $filePath),
                'file_name' => $filename,
                'file_path' => $filePath,
                'order' => 0,
                'status' => 1,
            ]);

            return response()->json([
                'success' => true,
                'id' => $upload->id,
                'url' => $upload->url,
                'message' => 'Dosya başarıyla yüklendi.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dosya yüklenirken bir hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete upload
     */
    public function destroy(Upload $upload)
    {
        try {
            // Dosyayı sil
            if (Storage::disk('public')->exists($upload->file_path)) {
                Storage::disk('public')->delete($upload->file_path);
            }

            // Kaydı sil
            $upload->delete();

            return response()->json([
                'success' => true,
                'message' => 'Dosya başarıyla silindi.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dosya silinirken bir hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload multiple files
     */
    public function uploadMultiple(Request $request)
    {
        $validated = $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|max:10240', // 10MB max
            'type' => 'nullable|string',
        ]);

        $uploads = [];
        $errors = [];

        foreach ($request->file('files') as $file) {
            try {
                $type = $request->get('type', 'general');
                $path = 'uploads/' . $type . '/' . date('Y/m');
                
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs($path, $filename, 'public');
                
                $upload = Upload::create([
                    'name' => $file->getClientOriginalName(),
                    'type' => $type,
                    'url' => asset('storage/' . $filePath),
                    'file_name' => $filename,
                    'file_path' => $filePath,
                    'order' => 0,
                    'status' => 1,
                ]);

                $uploads[] = $upload;
            } catch (\Exception $e) {
                $errors[] = [
                    'file' => $file->getClientOriginalName(),
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'success' => count($errors) === 0,
            'uploads' => $uploads,
            'errors' => $errors,
            'message' => count($uploads) . ' dosya yüklendi' . (count($errors) > 0 ? ', ' . count($errors) . ' hata oluştu' : ''),
        ]);
    }

    /**
     * Reorder uploads
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'uploads' => 'required|array',
            'uploads.*.id' => 'required|exists:uploads,id',
            'uploads.*.order' => 'required|integer|min:0',
        ]);

        try {
            foreach ($validated['uploads'] as $item) {
                Upload::where('id', $item['id'])
                    ->update(['order' => $item['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sıralama güncellendi.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sıralama güncellenirken bir hata oluştu.',
            ], 500);
        }
    }
}
