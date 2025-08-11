<?php

namespace App\Modules\ProductAttributes\Services;

use App\Modules\ProductAttributes\Models\ProductAttribute;
use App\Modules\AttributeCategories\Models\AttributeCategory;
use App\Modules\Products\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class ProductAttributeService
{
    /**
     * Tüm aktif özellikleri getir
     */
    public function getAllActive()
    {
        return ProductAttribute::active()
            ->with(['attributeCategory', 'image'])
            ->orderBy('order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Kategori ID'sine göre özellikleri getir
     */
    public function getByCategoryId($categoryId)
    {
        return ProductAttribute::active()
            ->where(function($query) use ($categoryId) {
                $query->whereJsonContains('product_category_ids', $categoryId)
                    ->orWhereNull('product_category_ids')
                    ->orWhere('product_category_ids', '[]');
            })
            ->with('attributeCategory')
            ->orderBy('order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Özellik kategorisine göre özellikleri getir
     */
    public function getByAttributeCategoryId($attributeCategoryId)
    {
        return ProductAttribute::active()
            ->where('attribute_category_id', $attributeCategoryId)
            ->orderBy('order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Birden fazla özelliği ID'lere göre getir
     */
    public function getByIds(array $ids)
    {
        return ProductAttribute::whereIn('id', $ids)
            ->with(['attributeCategory', 'image'])
            ->get()
            ->keyBy('id');
    }

    /**
     * Özellik değerlerini formatla
     */
    public function formatValues($values)
    {
        if (is_string($values)) {
            $values = json_decode($values, true) ?? [];
        }

        return array_values(array_filter($values));
    }

    /**
     * Ürün için uygun özellikleri getir
     */
    public function getForProduct(Product $product)
    {
        $categoryIds = [$product->category_id];
        
        // Alt kategorileri de dahil et
        if ($product->subcategory_id) {
            $categoryIds[] = $product->subcategory_id;
        }

        return ProductAttribute::active()
            ->where(function($query) use ($categoryIds) {
                foreach ($categoryIds as $categoryId) {
                    $query->orWhereJsonContains('product_category_ids', $categoryId);
                }
                $query->orWhereNull('product_category_ids')
                    ->orWhere('product_category_ids', '[]');
            })
            ->with('attributeCategory')
            ->orderBy('order')
            ->orderBy('name')
            ->get()
            ->groupBy('attribute_category_id');
    }

    /**
     * Özellik kombinasyonlarını oluştur (varyantlar için)
     */
    public function generateCombinations(array $attributes)
    {
        if (empty($attributes)) {
            return [];
        }

        $combinations = [[]];

        foreach ($attributes as $attributeId => $values) {
            $newCombinations = [];
            
            foreach ($combinations as $combination) {
                foreach ($values as $value) {
                    $newCombination = $combination;
                    $newCombination[$attributeId] = $value;
                    $newCombinations[] = $newCombination;
                }
            }
            
            $combinations = $newCombinations;
        }

        return $combinations;
    }

    /**
     * Varyant SKU oluştur
     */
    public function generateVariantSku($baseSku, array $attributeValues)
    {
        $suffix = '';
        
        foreach ($attributeValues as $value) {
            // İlk 3 karakteri al ve büyük harfe çevir
            $code = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $value), 0, 3));
            $suffix .= '-' . $code;
        }

        return $baseSku . $suffix;
    }

    /**
     * Özellik istatistiklerini getir
     */
    public function getStatistics()
    {
        return [
            'total_attributes' => ProductAttribute::count(),
            'active_attributes' => ProductAttribute::active()->count(),
            'total_values' => ProductAttribute::sum(DB::raw('JSON_LENGTH(values)')),
            'by_category' => AttributeCategory::withCount('productAttributes')->get(),
            'most_used' => $this->getMostUsedAttributes(),
        ];
    }

    /**
     * En çok kullanılan özellikleri getir
     */
    protected function getMostUsedAttributes($limit = 5)
    {
        // Bu, products tablosundaki attributes JSON alanını analiz eder
        $productAttributes = Product::whereNotNull('attributes')
            ->pluck('attributes')
            ->map(function ($attrs) {
                return array_keys(json_decode($attrs, true) ?? []);
            })
            ->flatten()
            ->countBy()
            ->sortDesc()
            ->take($limit);

        $attributeIds = $productAttributes->keys();
        
        $attributes = ProductAttribute::whereIn('id', $attributeIds)
            ->get()
            ->keyBy('id');

        return $productAttributes->map(function ($count, $id) use ($attributes) {
            return [
                'attribute' => $attributes->get($id),
                'usage_count' => $count
            ];
        })->filter(function ($item) {
            return $item['attribute'] !== null;
        });
    }

    /**
     * Özellik değerlerini doğrula
     */
    public function validateAttributeValues(array $attributes)
    {
        $errors = [];
        
        foreach ($attributes as $attributeId => $value) {
            $attribute = ProductAttribute::find($attributeId);
            
            if (!$attribute) {
                $errors[$attributeId] = 'Geçersiz özellik ID';
                continue;
            }
            
            if (!$attribute->status) {
                $errors[$attributeId] = 'Bu özellik aktif değil';
                continue;
            }
            
            if (!in_array($value, $attribute->values)) {
                $errors[$attributeId] = 'Geçersiz değer: ' . $value;
            }
        }
        
        return $errors;
    }

    /**
     * Filtre için özellikleri hazırla
     */
    public function prepareForFilter($categoryId = null)
    {
        $query = ProductAttribute::active()
            ->with(['attributeCategory']);

        if ($categoryId) {
            $query->where(function($q) use ($categoryId) {
                $q->whereJsonContains('product_category_ids', $categoryId)
                    ->orWhereNull('product_category_ids')
                    ->orWhere('product_category_ids', '[]');
            });
        }

        return $query->orderBy('order')
            ->orderBy('name')
            ->get()
            ->groupBy('attribute_category_id')
            ->map(function ($attributes, $categoryId) {
                $category = AttributeCategory::find($categoryId);
                
                return [
                    'category' => $category,
                    'attributes' => $attributes->map(function ($attr) {
                        return [
                            'id' => $attr->id,
                            'name' => $attr->name,
                            'values' => $attr->values,
                            'image' => $attr->image
                        ];
                    })
                ];
            });
    }

    /**
     * Ürün için özellik özeti oluştur
     */
    public function createProductAttributeSummary(Product $product)
    {
        if (!$product->attributes) {
            return '';
        }

        $attributes = json_decode($product->attributes, true);
        $attributeModels = $this->getByIds(array_keys($attributes));
        
        $summary = [];
        
        foreach ($attributes as $attrId => $value) {
            if ($attributeModels->has($attrId)) {
                $attr = $attributeModels->get($attrId);
                $summary[] = $attr->name . ': ' . $value;
            }
        }
        
        return implode(', ', $summary);
    }

    /**
     * Toplu özellik güncelleme
     */
    public function bulkUpdate(array $productIds, array $attributes)
    {
        DB::beginTransaction();
        
        try {
            foreach ($productIds as $productId) {
                $product = Product::find($productId);
                
                if ($product) {
                    $existingAttributes = json_decode($product->attributes, true) ?? [];
                    $mergedAttributes = array_merge($existingAttributes, $attributes);
                    
                    $product->attributes = json_encode($mergedAttributes);
                    $product->save();
                }
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Özellik arama
     */
    public function search($query)
    {
        return ProductAttribute::where('name', 'like', "%{$query}%")
            ->orWhereJsonContains('values', $query)
            ->with(['attributeCategory', 'image'])
            ->limit(10)
            ->get();
    }

    /**
     * Özellik klonla
     */
    public function clone(ProductAttribute $attribute, $newName = null)
    {
        $newAttribute = $attribute->replicate();
        $newAttribute->name = $newName ?? $attribute->name . ' (Kopya)';
        $newAttribute->save();
        
        // Resmi de kopyala
        if ($attribute->image) {
            $originalPath = $attribute->image->file_path;
            $newPath = 'product-attributes/' . uniqid() . '.' . pathinfo($originalPath, PATHINFO_EXTENSION);
            
            Storage::disk('public')->copy($originalPath, $newPath);
            
            $upload = \App\Modules\Uploads\Models\Upload::create([
                'name' => $attribute->image->name,
                'type' => 'product_attribute',
                'url' => asset('storage/' . $newPath),
                'file_name' => basename($newPath),
                'file_path' => $newPath,
                'order' => 0,
                'status' => 1,
            ]);
            
            $newAttribute->image_id = $upload->id;
            $newAttribute->save();
        }
        
        return $newAttribute;
    }
}
