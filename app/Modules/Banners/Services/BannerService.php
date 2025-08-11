<?php

namespace App\Modules\Banners\Services;

use App\Modules\Banners\Models\Banner;
use App\Modules\Uploads\Services\UploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class BannerService
{
    protected $uploadService;
    protected $cacheKey = 'banners';
    protected $cacheTime = 3600; // 1 hour

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Get filtered banners
     */
    public function getFiltered($filters = [], $perPage = 20)
    {
        $query = Banner::query();

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['position'])) {
            $query->where('position', $filters['position']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Active date filter
        if (isset($filters['active_only']) && $filters['active_only']) {
            $now = Carbon::now();
            $query->where(function($q) use ($now) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', $now);
            })->where(function($q) use ($now) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', $now);
            });
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
     * Get banners by position
     */
    public function getByPosition($position)
    {
        return Cache::remember($this->cacheKey . '_' . $position, $this->cacheTime, function() use ($position) {
            $now = Carbon::now();
            
            return Banner::where('position', $position)
                ->where('status', true)
                ->where(function($q) use ($now) {
                    $q->whereNull('start_date')
                        ->orWhere('start_date', '<=', $now);
                })->where(function($q) use ($now) {
                    $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', $now);
                })
                ->orderBy('order')
                ->get();
        });
    }

    /**
     * Create a new banner
     */
    public function create(array $data)
    {
        DB::beginTransaction();
        
        try {
            // Handle image upload
            if (isset($data['image'])) {
                $upload = $this->uploadService->upload($data['image'], 'banner', 'banners');
                $data['image_id'] = $upload->id;
                unset($data['image']);
            }
            
            // Handle mobile image upload
            if (isset($data['mobile_image'])) {
                $upload = $this->uploadService->upload($data['mobile_image'], 'banner_mobile', 'banners/mobile');
                $data['mobile_image_id'] = $upload->id;
                unset($data['mobile_image']);
            }
            
            // Create banner
            $banner = Banner::create($data);
            
            // Clear cache
            $this->clearCache();
            
            // Log activity
            activity()
                ->performedOn($banner)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $banner->toArray()])
                ->log('Banner oluşturuldu');
            
            DB::commit();
            return $banner;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update a banner
     */
    public function update(Banner $banner, array $data)
    {
        DB::beginTransaction();
        
        try {
            $oldAttributes = $banner->toArray();
            
            // Handle image upload
            if (isset($data['image'])) {
                // Delete old image
                if ($banner->image_id && $banner->image) {
                    $this->uploadService->delete($banner->image);
                }
                
                $upload = $this->uploadService->upload($data['image'], 'banner', 'banners');
                $data['image_id'] = $upload->id;
                unset($data['image']);
            }
            
            // Handle mobile image upload
            if (isset($data['mobile_image'])) {
                // Delete old mobile image
                if ($banner->mobile_image_id && $banner->mobileImage) {
                    $this->uploadService->delete($banner->mobileImage);
                }
                
                $upload = $this->uploadService->upload($data['mobile_image'], 'banner_mobile', 'banners/mobile');
                $data['mobile_image_id'] = $upload->id;
                unset($data['mobile_image']);
            }
            
            // Update banner
            $banner->update($data);
            
            // Clear cache
            $this->clearCache();
            
            // Log activity
            activity()
                ->performedOn($banner)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldAttributes,
                    'attributes' => $banner->toArray()
                ])
                ->log('Banner güncellendi');
            
            DB::commit();
            return $banner;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete a banner
     */
    public function delete(Banner $banner)
    {
        DB::beginTransaction();
        
        try {
            // Delete images
            if ($banner->image) {
                $this->uploadService->delete($banner->image);
            }
            
            if ($banner->mobileImage) {
                $this->uploadService->delete($banner->mobileImage);
            }
            
            // Log activity
            activity()
                ->performedOn($banner)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $banner->toArray()])
                ->log('Banner silindi');
            
            // Delete banner
            $banner->delete();
            
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
     * Update banner order
     */
    public function updateOrder(array $items)
    {
        DB::beginTransaction();
        
        try {
            foreach ($items as $item) {
                Banner::where('id', $item['id'])
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
     * Increment click count
     */
    public function incrementClickCount(Banner $banner)
    {
        $banner->increment('click_count');
        
        // Log activity
        activity()
            ->performedOn($banner)
            ->log('Banner tıklandı');
    }

    /**
     * Increment view count
     */
    public function incrementViewCount(Banner $banner)
    {
        $banner->increment('view_count');
    }

    /**
     * Get banner positions
     */
    public function getPositions()
    {
        return [
            'home_slider' => 'Ana Sayfa Slider',
            'home_top' => 'Ana Sayfa Üst',
            'home_middle' => 'Ana Sayfa Orta',
            'home_bottom' => 'Ana Sayfa Alt',
            'category_top' => 'Kategori Üst',
            'product_sidebar' => 'Ürün Yan Panel',
        ];
    }

    /**
     * Get banner statistics
     */
    public function getStatistics()
    {
        return [
            'total_banners' => Banner::count(),
            'active_banners' => Banner::where('status', true)->count(),
            'total_clicks' => Banner::sum('click_count'),
            'total_views' => Banner::sum('view_count'),
            'average_ctr' => $this->calculateAverageCTR(),
            'top_performing' => Banner::orderBy('click_count', 'desc')->limit(5)->get(),
            'by_position' => Banner::selectRaw('position, COUNT(*) as count')
                ->groupBy('position')
                ->pluck('count', 'position'),
        ];
    }

    /**
     * Calculate average CTR
     */
    protected function calculateAverageCTR()
    {
        $totalClicks = Banner::sum('click_count');
        $totalViews = Banner::sum('view_count');
        
        if ($totalViews == 0) {
            return 0;
        }
        
        return round(($totalClicks / $totalViews) * 100, 2);
    }

    /**
     * Clear banner cache
     */
    protected function clearCache()
    {
        Cache::forget($this->cacheKey);
        Cache::tags(['banners'])->flush();
        
        // Clear position-specific caches
        foreach ($this->getPositions() as $position => $name) {
            Cache::forget($this->cacheKey . '_' . $position);
        }
    }

    /**
     * Duplicate a banner
     */
    public function duplicate(Banner $banner)
    {
        DB::beginTransaction();
        
        try {
            $newBanner = $banner->replicate();
            $newBanner->title = $banner->title . ' (Copy)';
            $newBanner->click_count = 0;
            $newBanner->view_count = 0;
            $newBanner->status = false;
            $newBanner->save();
            
            // Copy images
            if ($banner->image) {
                $newUpload = $this->uploadService->duplicate($banner->image);
                $newBanner->image_id = $newUpload->id;
            }
            
            if ($banner->mobileImage) {
                $newMobileUpload = $this->uploadService->duplicate($banner->mobileImage);
                $newBanner->mobile_image_id = $newMobileUpload->id;
            }
            
            $newBanner->save();
            
            DB::commit();
            return $newBanner;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Get active banners
     */
    public function getActive()
    {
        $now = Carbon::now();
        
        return Banner::where('status', true)
            ->where(function($q) use ($now) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', $now);
            })->where(function($q) use ($now) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', $now);
            })
            ->orderBy('order')
            ->get();
    }

    /**
     * Get expired banners
     */
    public function getExpired()
    {
        return Banner::where('end_date', '<', Carbon::now())
            ->orderBy('end_date', 'desc')
            ->get();
    }
}
