<?php

namespace App\Modules\Brands\Services;

use App\Modules\Brands\Models\Brand;
use App\Modules\Uploads\Services\UploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BrandService
{
    protected $uploadService;
    protected $cacheKey = 'brands';
    protected $cacheTime = 3600; // 1 hour

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Get filtered brands
     */
    public function getFiltered($filters = [], $perPage = 20)
    {
        $query = Brand::withCount('products');

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        if (isset($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        $sortBy = $filters['sort_by'] ?? 'order';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        if ($perPage === 'all') {
            return $query->get();
        }

        return $query->paginate($perPage);
    }

    /**
     * Create a new brand
     */
    public function create(array $data)
    {
        DB::beginTransaction();
        
        try {
            // Generate unique slug
            $data['slug'] = $this->generateUniqueSlug($data['slug'] ?? $data['name']);
            
            // Handle logo upload
            if (isset($data['logo'])) {
                $upload = $this->uploadService->upload($data['logo'], 'brand', 'brands');
                $data['logo_id'] = $upload->id;
                unset($data['logo']);
            }
            
            // Create brand
            $brand = Brand::create($data);
            
            // Clear cache
            $this->clearCache();
            
            // Log activity
            activity()
                ->performedOn($brand)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $brand->toArray()])
                ->log('Marka oluşturuldu');
            
            DB::commit();
            return $brand;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update a brand
     */
    public function update(Brand $brand, array $data)
    {
        DB::beginTransaction();
        
        try {
            // Update slug if name changed
            if (isset($data['name']) && $data['name'] !== $brand->name) {
                $data['slug'] = $this->generateUniqueSlug($data['slug'] ?? $data['name'], $brand->id);
            }
            
            $oldAttributes = $brand->toArray();
            
            // Handle logo upload
            if (isset($data['logo'])) {
                // Delete old logo
                if ($brand->logo_id && $brand->logo) {
                    $this->uploadService->delete($brand->logo);
                }
                
                $upload = $this->uploadService->upload($data['logo'], 'brand', 'brands');
                $data['logo_id'] = $upload->id;
                unset($data['logo']);
            }
            
            // Update brand
            $brand->update($data);
            
            // Clear cache
            $this->clearCache();
            
            // Log activity
            activity()
                ->performedOn($brand)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldAttributes,
                    'attributes' => $brand->toArray()
                ])
                ->log('Marka güncellendi');
            
            DB::commit();
            return $brand;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete a brand
     */
    public function delete(Brand $brand)
    {
        DB::beginTransaction();
        
        try {
            // Check if brand has products
            if ($brand->products()->exists()) {
                throw new \Exception('Bu markaya ait ürünler bulunmaktadır. Önce ürünleri başka bir markaya taşıyın veya silin.');
            }
            
            // Delete logo
            if ($brand->logo) {
                $this->uploadService->delete($brand->logo);
            }
            
            // Log activity
            activity()
                ->performedOn($brand)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $brand->toArray()])
                ->log('Marka silindi');
            
            // Delete brand
            $brand->delete();
            
            // Clear cache
            $this->clearCache();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update brand order
     */
    public function updateOrder(array $items)
    {
        DB::beginTransaction();
        
        try {
            foreach ($items as $item) {
                Brand::where('id', $item['id'])
                    ->update(['order' => $item['order']]);
            }
            
            // Clear cache
            $this->clearCache();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Generate unique slug
     */
    protected function generateUniqueSlug($title, $excludeId = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug exists
     */
    protected function slugExists($slug, $excludeId = null)
    {
        $query = Brand::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    /**
     * Get featured brands
     */
    public function getFeatured($limit = 12)
    {
        return Cache::remember($this->cacheKey . '_featured_' . $limit, $this->cacheTime, function() use ($limit) {
            return Brand::where('status', true)
                ->where('is_featured', true)
                ->orderBy('order')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get active brands for dropdown
     */
    public function getForDropdown()
    {
        return Cache::remember($this->cacheKey . '_dropdown', $this->cacheTime, function() {
            return Brand::where('status', true)
                ->orderBy('name')
                ->pluck('name', 'id');
        });
    }

    /**
     * Get brand statistics
     */
    public function getStatistics()
    {
        return [
            'total_brands' => Brand::count(),
            'active_brands' => Brand::where('status', true)->count(),
            'featured_brands' => Brand::where('is_featured', true)->count(),
            'brands_with_products' => Brand::has('products')->count(),
            'countries' => Brand::distinct('country')->count('country'),
            'top_brands' => Brand::withCount('products')
                ->orderBy('products_count', 'desc')
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Search brands
     */
    public function search($query)
    {
        return Brand::where('status', true)
            ->where('name', 'like', "%{$query}%")
            ->limit(10)
            ->get();
    }

    /**
     * Clear brand cache
     */
    protected function clearCache()
    {
        Cache::forget($this->cacheKey);
        Cache::tags(['brands'])->flush();
    }

    /**
     * Get brands by country
     */
    public function getByCountry($country)
    {
        return Brand::where('status', true)
            ->where('country', $country)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get available countries
     */
    public function getAvailableCountries()
    {
        return Brand::where('status', true)
            ->whereNotNull('country')
            ->distinct()
            ->pluck('country');
    }

    /**
     * Import brands from CSV
     */
    public function importFromCsv($file)
    {
        $data = array_map('str_getcsv', file($file));
        $headers = array_shift($data);
        
        $imported = 0;
        $failed = 0;
        
        foreach ($data as $row) {
            try {
                $brandData = array_combine($headers, $row);
                $this->create([
                    'name' => $brandData['name'],
                    'description' => $brandData['description'] ?? null,
                    'website' => $brandData['website'] ?? null,
                    'country' => $brandData['country'] ?? null,
                    'status' => true,
                ]);
                $imported++;
            } catch (\Exception $e) {
                $failed++;
            }
        }
        
        return [
            'imported' => $imported,
            'failed' => $failed,
        ];
    }
}
