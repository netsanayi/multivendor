<?php

namespace App\Modules\Uploads\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Upload extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'file_type',
        'extension',
        'relation_id',
        'url',
        'file_name',
        'file_path',
        'thumbnail_path',
        'mime_type',
        'file_size',
        'size',
        'metadata',
        'order',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'relation_id' => 'integer',
        'file_size' => 'integer',
        'size' => 'integer',
        'order' => 'integer',
        'status' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::deleting(function ($upload) {
            // Ana dosyayı diskten sil
            if ($upload->file_path && Storage::disk('public')->exists($upload->file_path)) {
                Storage::disk('public')->delete($upload->file_path);
            }
            
            // Thumbnail varsa onu da sil
            if ($upload->thumbnail_path && Storage::disk('public')->exists($upload->thumbnail_path)) {
                Storage::disk('public')->delete($upload->thumbnail_path);
            }
        });
    }

    /**
     * Scope a query to only include active uploads.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to only include uploads of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include uploads for a specific relation.
     */
    public function scopeForRelation($query, $type, $relationId)
    {
        return $query->where('type', $type)->where('relation_id', $relationId);
    }

    /**
     * Get the full URL of the upload.
     */
    public function getFullUrlAttribute()
    {
        if (filter_var($this->url, FILTER_VALIDATE_URL)) {
            return $this->url;
        }
        
        return Storage::url($this->file_path);
    }

    /**
     * Get the file extension.
     */
    public function getExtensionAttribute()
    {
        if ($this->attributes['extension'] ?? null) {
            return $this->attributes['extension'];
        }
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    /**
     * Check if the upload is an image.
     */
    public function isImage(): bool
    {
        return in_array($this->mime_type, [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
        ]);
    }

    /**
     * Check if the upload is a video.
     */
    public function isVideo(): bool
    {
        return in_array($this->mime_type, [
            'video/mp4',
            'video/mpeg',
            'video/quicktime',
            'video/x-msvideo',
            'video/x-flv',
            'video/webm',
        ]);
    }

    /**
     * Check if the upload is a document.
     */
    public function isDocument(): bool
    {
        return in_array($this->mime_type, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Get human readable file size.
     */
    public function getHumanFileSizeAttribute()
    {
        $bytes = $this->size ?? $this->file_size ?? 0;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get thumbnail URL if exists.
     */
    public function getThumbnailUrlAttribute()
    {
        if (!$this->isImage()) {
            return null;
        }

        if ($this->thumbnail_path) {
            return asset('storage/' . $this->thumbnail_path);
        }

        // Thumbnail yoksa orijinal URL'i döndür
        return $this->full_url;
    }
    
    /**
     * Generate thumbnail URL if image.
     */
    public function getThumbnailUrl($width = 150, $height = 150)
    {
        return $this->thumbnail_url ?? $this->full_url;
    }

    /**
     * Get related model based on type.
     */
    public function getRelatedModelAttribute()
    {
        switch ($this->type) {
            case 'Kategori':
                return \App\Modules\Categories\Models\Category::find($this->relation_id);
            case 'Ürün':
                return \App\Modules\Products\Models\Product::find($this->relation_id);
            case 'Marka':
                return \App\Modules\Brands\Models\Brand::find($this->relation_id);
            case 'Kullanıcı':
                return \App\Modules\Users\Models\User::find($this->relation_id);
            case 'Ürün Özelliği':
                return \App\Modules\ProductAttributes\Models\ProductAttribute::find($this->relation_id);
            case 'Müşteri Ürünleri':
                return \App\Modules\VendorProducts\Models\VendorProduct::find($this->relation_id);
            case 'Banner':
                return \App\Modules\Banners\Models\Banner::find($this->relation_id);
            default:
                return null;
        }
    }
}
