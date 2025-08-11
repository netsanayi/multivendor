<?php

namespace App\Modules\Uploads\Services;

use App\Modules\Uploads\Models\Upload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class UploadService
{
    /**
     * İzin verilen dosya tipleri
     */
    protected $allowedTypes = [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
        'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'],
        'video' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv'],
        'audio' => ['mp3', 'wav', 'ogg', 'flac', 'aac'],
        'archive' => ['zip', 'rar', '7z', 'tar', 'gz']
    ];

    /**
     * Maksimum dosya boyutları (MB)
     */
    protected $maxSizes = [
        'image' => 5,
        'document' => 10,
        'video' => 100,
        'audio' => 20,
        'archive' => 50
    ];

    /**
     * Dosya yükle
     */
    public function upload(UploadedFile $file, $type = 'general', $folder = null)
    {
        // Dosya tipi kontrolü
        $extension = strtolower($file->getClientOriginalExtension());
        $fileType = $this->getFileType($extension);
        
        if (!$fileType) {
            throw new \Exception('Bu dosya tipi desteklenmiyor: ' . $extension);
        }

        // Dosya boyutu kontrolü
        $maxSize = $this->maxSizes[$fileType] ?? 10;
        if ($file->getSize() > $maxSize * 1024 * 1024) {
            throw new \Exception("Dosya boyutu {$maxSize}MB'dan büyük olamaz.");
        }

        // Klasör yapısını oluştur
        if (!$folder) {
            $folder = $type . '/' . date('Y/m');
        }

        // Benzersiz dosya adı oluştur
        $fileName = $this->generateFileName($file);
        
        // Dosyayı yükle
        $path = $file->storeAs($folder, $fileName, 'public');

        // Resim ise thumbnail oluştur
        $thumbnailPath = null;
        if ($fileType === 'image' && $extension !== 'svg') {
            $thumbnailPath = $this->createThumbnail($path, $folder, $fileName);
        }

        // Veritabanına kaydet
        $upload = Upload::create([
            'name' => $file->getClientOriginalName(),
            'type' => $type,
            'file_type' => $fileType,
            'extension' => $extension,
            'url' => asset('storage/' . $path),
            'file_name' => $fileName,
            'file_path' => $path,
            'thumbnail_path' => $thumbnailPath,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'order' => 0,
            'status' => 1,
            'metadata' => $this->extractMetadata($file, $fileType)
        ]);

        return $upload;
    }

    /**
     * Birden fazla dosya yükle
     */
    public function uploadMultiple(array $files, $type = 'general', $folder = null)
    {
        $uploads = [];
        
        DB::beginTransaction();
        try {
            foreach ($files as $index => $file) {
                if ($file instanceof UploadedFile) {
                    $upload = $this->upload($file, $type, $folder);
                    $upload->order = $index;
                    $upload->save();
                    $uploads[] = $upload;
                }
            }
            
            DB::commit();
            return $uploads;
        } catch (\Exception $e) {
            DB::rollback();
            
            // Yüklenen dosyaları temizle
            foreach ($uploads as $upload) {
                $this->delete($upload);
            }
            
            throw $e;
        }
    }

    /**
     * Dosya sil
     */
    public function delete(Upload $upload)
    {
        // Ana dosyayı sil
        if (Storage::disk('public')->exists($upload->file_path)) {
            Storage::disk('public')->delete($upload->file_path);
        }

        // Thumbnail varsa sil
        if ($upload->thumbnail_path && Storage::disk('public')->exists($upload->thumbnail_path)) {
            Storage::disk('public')->delete($upload->thumbnail_path);
        }

        // Veritabanından sil
        return $upload->delete();
    }

    /**
     * Thumbnail oluştur
     */
    protected function createThumbnail($path, $folder, $fileName)
    {
        $fullPath = storage_path('app/public/' . $path);
        
        if (!file_exists($fullPath)) {
            return null;
        }

        try {
            $thumbnailFolder = $folder . '/thumbnails';
            $thumbnailName = 'thumb_' . $fileName;
            $thumbnailPath = $thumbnailFolder . '/' . $thumbnailName;
            
            // Thumbnail klasörünü oluştur
            Storage::disk('public')->makeDirectory($thumbnailFolder);
            
            // Resmi yeniden boyutlandır
            $image = Image::make($fullPath);
            
            // Maksimum genişlik/yükseklik 300px
            $image->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            
            // Thumbnail'i kaydet
            $thumbnailFullPath = storage_path('app/public/' . $thumbnailPath);
            $image->save($thumbnailFullPath, 80);
            
            return $thumbnailPath;
        } catch (\Exception $e) {
            // Hata durumunda null döndür
            return null;
        }
    }

    /**
     * Dosya tipini belirle
     */
    protected function getFileType($extension)
    {
        foreach ($this->allowedTypes as $type => $extensions) {
            if (in_array($extension, $extensions)) {
                return $type;
            }
        }
        
        return null;
    }

    /**
     * Benzersiz dosya adı oluştur
     */
    protected function generateFileName(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $uniqueId = uniqid();
        
        return "{$name}_{$uniqueId}.{$extension}";
    }

    /**
     * Dosya metadatalarını çıkar
     */
    protected function extractMetadata(UploadedFile $file, $fileType)
    {
        $metadata = [
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType()
        ];

        // Resim için ek metadata
        if ($fileType === 'image') {
            try {
                $imageInfo = getimagesize($file->getRealPath());
                if ($imageInfo) {
                    $metadata['width'] = $imageInfo[0];
                    $metadata['height'] = $imageInfo[1];
                    $metadata['aspect_ratio'] = round($imageInfo[0] / $imageInfo[1], 2);
                }
                
                // EXIF verilerini oku (sadece JPEG için)
                if ($file->getMimeType() === 'image/jpeg' && function_exists('exif_read_data')) {
                    $exif = @exif_read_data($file->getRealPath());
                    if ($exif) {
                        $metadata['exif'] = [
                            'camera' => $exif['Make'] ?? null,
                            'model' => $exif['Model'] ?? null,
                            'date_taken' => $exif['DateTimeOriginal'] ?? null,
                            'iso' => $exif['ISOSpeedRatings'] ?? null,
                            'aperture' => $exif['FNumber'] ?? null,
                            'exposure' => $exif['ExposureTime'] ?? null,
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Metadata çıkarma hatası, devam et
            }
        }

        return $metadata;
    }

    /**
     * URL'den dosya yükle
     */
    public function uploadFromUrl($url, $type = 'general', $folder = null)
    {
        // URL'den dosya içeriğini al
        $content = @file_get_contents($url);
        
        if (!$content) {
            throw new \Exception('URL\'den dosya alınamadı: ' . $url);
        }

        // Geçici dosya oluştur
        $tempFile = tempnam(sys_get_temp_dir(), 'upload_');
        file_put_contents($tempFile, $content);

        // Dosya adını ve uzantısını belirle
        $fileName = basename(parse_url($url, PHP_URL_PATH));
        if (!$fileName) {
            $fileName = 'download_' . uniqid();
        }

        // UploadedFile nesnesi oluştur
        $file = new UploadedFile(
            $tempFile,
            $fileName,
            mime_content_type($tempFile),
            null,
            true
        );

        // Normal yükleme işlemini yap
        $upload = $this->upload($file, $type, $folder);

        // Geçici dosyayı sil
        @unlink($tempFile);

        return $upload;
    }

    /**
     * Dosya boyutunu optimize et (resimler için)
     */
    public function optimizeImage(Upload $upload, $maxWidth = 1920, $maxHeight = 1080, $quality = 85)
    {
        if ($upload->file_type !== 'image' || $upload->extension === 'svg') {
            return false;
        }

        $fullPath = storage_path('app/public/' . $upload->file_path);
        
        if (!file_exists($fullPath)) {
            return false;
        }

        try {
            $image = Image::make($fullPath);
            
            // Boyutları kontrol et
            if ($image->width() > $maxWidth || $image->height() > $maxHeight) {
                $image->resize($maxWidth, $maxHeight, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            
            // Optimize edilmiş versiyonu kaydet
            $image->save($fullPath, $quality);
            
            // Yeni dosya boyutunu güncelle
            $upload->size = filesize($fullPath);
            $upload->metadata = array_merge($upload->metadata ?? [], [
                'optimized' => true,
                'optimized_at' => now()->toDateTimeString(),
                'quality' => $quality
            ]);
            $upload->save();
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Dosya taşı
     */
    public function move(Upload $upload, $newFolder)
    {
        $oldPath = $upload->file_path;
        $newPath = $newFolder . '/' . $upload->file_name;
        
        // Dosyayı taşı
        if (Storage::disk('public')->move($oldPath, $newPath)) {
            // Thumbnail varsa onu da taşı
            if ($upload->thumbnail_path) {
                $oldThumbnailPath = $upload->thumbnail_path;
                $newThumbnailPath = $newFolder . '/thumbnails/thumb_' . $upload->file_name;
                
                Storage::disk('public')->move($oldThumbnailPath, $newThumbnailPath);
                $upload->thumbnail_path = $newThumbnailPath;
            }
            
            // Veritabanını güncelle
            $upload->file_path = $newPath;
            $upload->url = asset('storage/' . $newPath);
            $upload->save();
            
            return true;
        }
        
        return false;
    }

    /**
     * Dosya kopyala
     */
    public function duplicate(Upload $upload)
    {
        // Yeni dosya adı oluştur
        $extension = pathinfo($upload->file_name, PATHINFO_EXTENSION);
        $name = pathinfo($upload->file_name, PATHINFO_FILENAME);
        $newFileName = $name . '_copy_' . uniqid() . '.' . $extension;
        
        // Dosyayı kopyala
        $folder = dirname($upload->file_path);
        $newPath = $folder . '/' . $newFileName;
        
        if (Storage::disk('public')->copy($upload->file_path, $newPath)) {
            // Thumbnail varsa onu da kopyala
            $thumbnailPath = null;
            if ($upload->thumbnail_path) {
                $newThumbnailPath = $folder . '/thumbnails/thumb_' . $newFileName;
                Storage::disk('public')->copy($upload->thumbnail_path, $newThumbnailPath);
                $thumbnailPath = $newThumbnailPath;
            }
            
            // Yeni kayıt oluştur
            $newUpload = $upload->replicate();
            $newUpload->file_name = $newFileName;
            $newUpload->file_path = $newPath;
            $newUpload->thumbnail_path = $thumbnailPath;
            $newUpload->url = asset('storage/' . $newPath);
            $newUpload->save();
            
            return $newUpload;
        }
        
        return null;
    }

    /**
     * İstatistikleri getir
     */
    public function getStatistics()
    {
        $totalFiles = Upload::count();
        $totalSize = Upload::sum('size');
        
        $byType = Upload::selectRaw('file_type, COUNT(*) as count, SUM(size) as total_size')
            ->groupBy('file_type')
            ->get();
        
        $recentUploads = Upload::latest()
            ->limit(10)
            ->get();
        
        return [
            'total_files' => $totalFiles,
            'total_size' => $this->formatBytes($totalSize),
            'total_size_bytes' => $totalSize,
            'by_type' => $byType->map(function ($item) {
                return [
                    'type' => $item->file_type,
                    'count' => $item->count,
                    'size' => $this->formatBytes($item->total_size),
                    'size_bytes' => $item->total_size
                ];
            }),
            'recent_uploads' => $recentUploads
        ];
    }

    /**
     * Byte'ı okunabilir formata çevir
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
