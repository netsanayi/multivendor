<?php

namespace App\Modules\Categories\Services;

use App\Modules\Categories\Models\Category;
use App\Modules\Uploads\Services\UploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryService
{
    protected $uploadService;
    protected $cacheKey = 'categories_tree';
    protected $cacheTime = 3600; // 1 hour

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Get all categories with filters
     */
    public function getFiltered($filters = [])
    {
        $query = Category::with(['parent', 'children']);

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['parent_id'])) {
            if ($filters['parent_id'] === 'root') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $filters['parent_id']);
            }
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        return $query->orderBy('order')->orderBy('name')->get();
    }

    /**
     * Get category tree
     */
    public function getTree($parentId = null, $withInactive = false)
    {
        return Cache::remember($this->cacheKey . '_' . $parentId . '_' . $withInactive, $this->cacheTime, function() use ($parentId, $withInactive) {
            $query = Category::where('parent_id', $parentId);
            
            if (!$withInactive) {
                $query->where('status', true);
            }
            
            $categories = $query->orderBy('order')->orderBy('name')->get();
            
            foreach ($categories as $category) {
                $category->children = $this->getTree($category->id, $withInactive);
            }
            
            return $categories;
        });
    }

    /**
     * Get flattened tree for dropdown
     */
    public function getFlatTree($parentId = null, $level = 0, $excludeId = null)
    {
        $categories = Category::where('parent_id', $parentId)
            ->where('id', '!=', $excludeId)
            ->orderBy('order')
            ->orderBy('name')
            ->get();
        
        $result = [];
        
        foreach ($categories as $category) {
            $category->level = $level;
            $category->name_with_prefix = str_repeat('— ', $level) . $category->name;
            $result[] = $category;
            
            $children = $this->getFlatTree($category->id, $level + 1, $excludeId);
            $result = array_merge($result, $children);
        }
        
        return $result;
    }

    /**
     * Create a new category
     */
    public function create(array $data)
    {
        DB::beginTransaction();
        
        try {
            // Generate unique slug
            $data['slug'] = $this->generateUniqueSlug($data['slug'] ?? $data['name']);
            
            // Handle image upload
            if (isset($data['image'])) {
                $upload = $this->uploadService->upload($data['image'], 'category', 'categories');
                $data['image_id'] = $upload->id;
                unset($data['image']);
            }
            
            // Create category
            $category = Category::create($data);
            
            // Clear cache
            $this->clearCache();
            
            // Log activity
            activity()
                ->performedOn($category)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $category->toArray()])
                ->log('Kategori oluşturuldu');
            
            DB::commit();
            return $category;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update a category
     */
    public function update(Category $category, array $data)
    {
        DB::beginTransaction();
        
        try {
            // Update slug if name changed
            if (isset($data['name']) && $data['name'] !== $category->name) {
                $data['slug'] = $this->generateUniqueSlug($data['slug'] ?? $data['name'], $category->id);
            }
            
            $oldAttributes = $category->toArray();
            
            // Handle image upload
            if (isset($data['image'])) {
                // Delete old image
                if ($category->image_id && $category->image) {
                    $this->uploadService->delete($category->image);
                }
                
                $upload = $this->uploadService->upload($data['image'], 'category', 'categories');
                $data['image_id'] = $upload->id;
                unset($data['image']);
            }
            
            // Update category
            $category->update($data);
            
            // Clear cache
            $this->clearCache();
            
            // Log activity
            activity()
                ->performedOn($category)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldAttributes,
                    'attributes' => $category->toArray()
                ])
                ->log('Kategori güncellendi');
            
            DB::commit();
            return $category;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete a category
     */
    public function delete(Category $category)
    {
        DB::beginTransaction();
        
        try {
            // Check if category has products
            if ($category->products()->exists()) {
                throw new \Exception('Bu kategoride ürünler bulunmaktadır. Önce ürünleri başka bir kategoriye taşıyın.');
            }
            
            // Check if category has children
            if ($category->children()->exists()) {
                throw new \Exception('Bu kategorinin alt kategorileri bulunmaktadır. Önce alt kategorileri silin veya taşıyın.');
            }
            
            // Delete image
            if ($category->image) {
                $this->uploadService->delete($category->image);
            }
            
            // Log activity
            activity()
                ->performedOn($category)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $category->toArray()])
                ->log('Kategori silindi');
            
            // Delete category
            $category->delete();
            
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
     * Move category to another parent
     */
    public function move(Category $category, $newParentId)
    {
        // Check if new parent is not a descendant
        if ($newParentId && $this->isDescendant($category, $newParentId)) {
            throw new \Exception('Hedef kategori, bu kategorinin alt kategorisi olamaz.');
        }
        
        $category->parent_id = $newParentId;
        $category->save();
        
        // Clear cache
        $this->clearCache();
        
        return $category;
    }

    /**
     * Update category order
     */
    public function updateOrder(array $items)
    {
        DB::beginTransaction();
        
        try {
            foreach ($items as $item) {
                Category::where('id', $item['id'])
                    ->update([
                        'order' => $item['order'],
                        'parent_id' => $item['parent_id'] ?? null
                    ]);
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
        $query = Category::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    /**
     * Check if a category is a descendant of another
     */
    protected function isDescendant(Category $category, $potentialParentId)
    {
        $descendants = $this->getAllDescendants($category);
        return in_array($potentialParentId, $descendants);
    }

    /**
     * Get all descendant IDs of a category
     */
    protected function getAllDescendants(Category $category)
    {
        $descendants = [];
        
        foreach ($category->children as $child) {
            $descendants[] = $child->id;
            $descendants = array_merge($descendants, $this->getAllDescendants($child));
        }
        
        return $descendants;
    }

    /**
     * Get breadcrumb for category
     */
    public function getBreadcrumb(Category $category)
    {
        $breadcrumb = [];
        $current = $category;
        
        while ($current) {
            array_unshift($breadcrumb, $current);
            $current = $current->parent;
        }
        
        return $breadcrumb;
    }

    /**
     * Get category statistics
     */
    public function getStatistics()
    {
        return [
            'total_categories' => Category::count(),
            'active_categories' => Category::where('status', true)->count(),
            'root_categories' => Category::whereNull('parent_id')->count(),
            'featured_categories' => Category::where('is_featured', true)->count(),
            'categories_with_products' => Category::has('products')->count(),
            'empty_categories' => Category::doesntHave('products')->count(),
            'max_depth' => $this->getMaxDepth(),
        ];
    }

    /**
     * Get maximum category depth
     */
    protected function getMaxDepth($parentId = null, $currentDepth = 0)
    {
        $categories = Category::where('parent_id', $parentId)->get();
        
        if ($categories->isEmpty()) {
            return $currentDepth;
        }
        
        $maxChildDepth = $currentDepth;
        
        foreach ($categories as $category) {
            $childDepth = $this->getMaxDepth($category->id, $currentDepth + 1);
            $maxChildDepth = max($maxChildDepth, $childDepth);
        }
        
        return $maxChildDepth;
    }

    /**
     * Clear category cache
     */
    protected function clearCache()
    {
        Cache::forget($this->cacheKey);
        Cache::tags(['categories'])->flush();
    }

    /**
     * Get featured categories
     */
    public function getFeatured($limit = 6)
    {
        return Category::where('status', true)
            ->where('is_featured', true)
            ->orderBy('order')
            ->limit($limit)
            ->get();
    }

    /**
     * Get menu categories
     */
    public function getMenuCategories()
    {
        return Category::where('status', true)
            ->where('show_in_menu', true)
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->where('status', true)
                    ->where('show_in_menu', true)
                    ->orderBy('order');
            }])
            ->orderBy('order')
            ->get();
    }
}
