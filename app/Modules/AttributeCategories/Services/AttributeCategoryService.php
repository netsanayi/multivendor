<?php

namespace App\Modules\AttributeCategories\Services;

use App\Modules\AttributeCategories\Models\AttributeCategory;
use App\Modules\ProductAttributes\Models\ProductAttribute;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class AttributeCategoryService
{
    /**
     * Tüm aktif özellik kategorilerini getir
     */
    public function getAllActive()
    {
        return AttributeCategory::active()
            ->withCount('productAttributes')
            ->orderBy('order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Özellik kategorisi ağacını oluştur
     */
    public function buildTree()
    {
        $categories = AttributeCategory::orderBy('order')
            ->orderBy('name')
            ->get();

        return $this->buildNestedTree($categories);
    }

    /**
     * İç içe kategori ağacı oluştur
     */
    protected function buildNestedTree(Collection $categories, $parentId = null)
    {
        $branch = [];

        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $children = $this->buildNestedTree($categories, $category->id);
                
                if ($children) {
                    $category->children = $children;
                }
                
                $branch[] = $category;
            }
        }

        return $branch;
    }

    /**
     * Dropdown için kategori listesi hazırla
     */
    public function getForDropdown($excludeId = null)
    {
        $categories = AttributeCategory::orderBy('order')
            ->orderBy('name')
            ->get();

        $options = [];
        
        foreach ($categories as $category) {
            if ($excludeId && $category->id == $excludeId) {
                continue;
            }
            
            $prefix = $category->parent_id ? '— ' : '';
            $options[$category->id] = $prefix . $category->name;
        }

        return $options;
    }

    /**
     * Kategori istatistiklerini getir
     */
    public function getStatistics()
    {
        $totalCategories = AttributeCategory::count();
        $activeCategories = AttributeCategory::active()->count();
        
        $categoriesWithAttributes = AttributeCategory::withCount('productAttributes')
            ->having('product_attributes_count', '>', 0)
            ->count();

        $topCategories = AttributeCategory::withCount('productAttributes')
            ->orderBy('product_attributes_count', 'desc')
            ->limit(5)
            ->get();

        return [
            'total' => $totalCategories,
            'active' => $activeCategories,
            'with_attributes' => $categoriesWithAttributes,
            'empty' => $totalCategories - $categoriesWithAttributes,
            'top_categories' => $topCategories
        ];
    }

    /**
     * Kategori slug oluştur
     */
    public function generateSlug($name, $excludeId = null)
    {
        $slug = \Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Slug kontrolü
     */
    protected function slugExists($slug, $excludeId = null)
    {
        $query = AttributeCategory::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    /**
     * Kategoriyi alt kategorileriyle birlikte sil
     */
    public function deleteWithChildren(AttributeCategory $category)
    {
        DB::beginTransaction();
        
        try {
            // Alt kategorileri bul
            $childIds = $this->getAllChildIds($category->id);
            
            // Önce bu kategorilere ait özellikleri kontrol et
            $hasAttributes = ProductAttribute::whereIn('attribute_category_id', array_merge([$category->id], $childIds))
                ->exists();
            
            if ($hasAttributes) {
                throw new \Exception('Bu kategori veya alt kategorilerinde özellikler bulunmaktadır.');
            }
            
            // Alt kategorileri sil
            if (!empty($childIds)) {
                AttributeCategory::whereIn('id', $childIds)->delete();
            }
            
            // Ana kategoriyi sil
            $category->delete();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Tüm alt kategori ID'lerini getir
     */
    protected function getAllChildIds($parentId)
    {
        $childIds = [];
        
        $children = AttributeCategory::where('parent_id', $parentId)->pluck('id');
        
        foreach ($children as $childId) {
            $childIds[] = $childId;
            $childIds = array_merge($childIds, $this->getAllChildIds($childId));
        }
        
        return $childIds;
    }

    /**
     * Kategori sıralamasını güncelle
     */
    public function updateOrder(array $items)
    {
        DB::beginTransaction();
        
        try {
            foreach ($items as $item) {
                AttributeCategory::where('id', $item['id'])
                    ->update([
                        'order' => $item['order'],
                        'parent_id' => $item['parent_id'] ?? null
                    ]);
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Kategori yolu (breadcrumb) oluştur
     */
    public function getBreadcrumb(AttributeCategory $category)
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
     * Kategori ağacını düzleştir
     */
    public function flattenTree($tree = null, $level = 0)
    {
        if ($tree === null) {
            $tree = $this->buildTree();
        }
        
        $result = [];
        
        foreach ($tree as $node) {
            $node->level = $level;
            $result[] = $node;
            
            if (isset($node->children) && count($node->children) > 0) {
                $result = array_merge($result, $this->flattenTree($node->children, $level + 1));
            }
        }
        
        return $result;
    }

    /**
     * En popüler kategorileri getir
     */
    public function getMostPopular($limit = 10)
    {
        return AttributeCategory::withCount('productAttributes')
            ->having('product_attributes_count', '>', 0)
            ->orderBy('product_attributes_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Kategori arama
     */
    public function search($query)
    {
        return AttributeCategory::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->withCount('productAttributes')
            ->limit(10)
            ->get();
    }

    /**
     * Kategorileri birleştir
     */
    public function merge(AttributeCategory $source, AttributeCategory $target)
    {
        DB::beginTransaction();
        
        try {
            // Özellikleri hedef kategoriye taşı
            ProductAttribute::where('attribute_category_id', $source->id)
                ->update(['attribute_category_id' => $target->id]);
            
            // Alt kategorileri taşı
            AttributeCategory::where('parent_id', $source->id)
                ->update(['parent_id' => $target->id]);
            
            // Kaynak kategoriyi sil
            $source->delete();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Kategori klonla
     */
    public function clone(AttributeCategory $category, $newName = null)
    {
        $newCategory = $category->replicate();
        $newCategory->name = $newName ?? $category->name . ' (Kopya)';
        $newCategory->slug = $this->generateSlug($newCategory->name);
        $newCategory->save();
        
        // Alt kategorileri de klonla
        $children = AttributeCategory::where('parent_id', $category->id)->get();
        
        foreach ($children as $child) {
            $this->cloneWithParent($child, $newCategory->id);
        }
        
        return $newCategory;
    }

    /**
     * Alt kategoriyi belirtilen parent ile klonla
     */
    protected function cloneWithParent(AttributeCategory $category, $parentId)
    {
        $newCategory = $category->replicate();
        $newCategory->parent_id = $parentId;
        $newCategory->slug = $this->generateSlug($newCategory->name);
        $newCategory->save();
        
        // Bu kategorinin alt kategorilerini de klonla
        $children = AttributeCategory::where('parent_id', $category->id)->get();
        
        foreach ($children as $child) {
            $this->cloneWithParent($child, $newCategory->id);
        }
        
        return $newCategory;
    }
}
