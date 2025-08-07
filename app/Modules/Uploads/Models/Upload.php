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
        'relation_id',
        'url',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
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
        'order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::deleting(function ($upload) {
            // Dosyayı diskten sil
            if ($upload->file_path && Storage::exists($upload->file_path)) {
                Storage::delete($upload->file_path);
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
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Generate thumbnail URL if image.
     */
    public function getThumbnailUrl($width = 150, $height = 150)
    {
        if (!$this->isImage()) {
            return null;
        }

        // Burada thumbnail oluşturma mantığı eklenebilir
        // Şimdilik orijinal URL'i döndürüyoruz
        return $this->full_url;
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
