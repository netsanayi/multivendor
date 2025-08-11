<?php

namespace App\Modules\Products\Services;

use App\Modules\Products\Models\Product;
use App\Modules\Uploads\Services\UploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Get filtered products
     */
    public function getFiltered($filters = [], $perPage = 20)
    {
        $query = Product::with(['category', 'brand', 'images']);

        // Search
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Brand filter
        if (isset($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        // Status filter
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Featured filter
        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        // Price range filter
        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Stock filter
        if (isset($filters['in_stock'])) {
            if ($filters['in_stock']) {
                $query->where('quantity', '>', 0);
            } else {
                $query->where('quantity', '=', 0);
            }
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Create a new product
     */
    public function create(array $data)
    {
        DB::beginTransaction();
        
        try {
            // Generate unique slug
            $data['slug'] = $this->generateUniqueSlug($data['slug'] ?? $data['name']);
            
            // Create product
            $product = Product::create($data);
            
            // Handle images
            if (isset($data['images'])) {
                foreach ($data['images'] as $index => $image) {
                    $upload = $this->uploadService->upload($image, 'product', 'products/' . $product->id);
                    $upload->relation_id = $product->id;
                    $upload->order = $index;
                    $upload->save();
                }
            }
            
            // Handle featured image
            if (isset($data['featured_image'])) {
                $upload = $this->uploadService->upload($data['featured_image'], 'product_featured', 'products/' . $product->id);
                $product->featured_image_id = $upload->id;
                $product->save();
            }
            
            // Handle tags
            if (isset($data['tags'])) {
                $this->syncTags($product, $data['tags']);
            }
            
            // Handle attributes
            if (isset($data['attributes'])) {
                $product->attributes = json_encode($data['attributes']);
                $product->save();
            }
            
            // Log activity
            activity()
                ->performedOn($product)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $product->toArray()])
                ->log('Ürün oluşturuldu');
            
            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update a product
     */
    public function update(Product $product, array $data)
    {
        DB::beginTransaction();
        
        try {
            // Update slug if name changed
            if (isset($data['name']) && $data['name'] !== $product->name) {
                $data['slug'] = $this->generateUniqueSlug($data['slug'] ?? $data['name'], $product->id);
            }
            
            $oldAttributes = $product->toArray();
            
            // Update product
            $product->update($data);
            
            // Handle new images
            if (isset($data['images'])) {
                $currentMaxOrder = $product->images()->max('order') ?? -1;
                foreach ($data['images'] as $index => $image) {
                    $upload = $this->uploadService->upload($image, 'product', 'products/' . $product->id);
                    $upload->relation_id = $product->id;
                    $upload->order = $currentMaxOrder + $index + 1;
                    $upload->save();
                }
            }
            
            // Handle removed images
            if (isset($data['remove_images'])) {
                foreach ($data['remove_images'] as $imageId) {
                    $image = $product->images()->find($imageId);
                    if ($image) {
                        $this->uploadService->delete($image);
                    }
                }
            }
            
            // Handle featured image
            if (isset($data['featured_image'])) {
                // Delete old featured image
                if ($product->featured_image_id && $product->featuredImage) {
                    $this->uploadService->delete($product->featuredImage);
                }
                
                $upload = $this->uploadService->upload($data['featured_image'], 'product_featured', 'products/' . $product->id);
                $product->featured_image_id = $upload->id;
                $product->save();
            }
            
            // Handle tags
            if (isset($data['tags'])) {
                $this->syncTags($product, $data['tags']);
            }
            
            // Handle attributes
            if (isset($data['attributes'])) {
                $product->attributes = json_encode($data['attributes']);
                $product->save();
            }
            
            // Log activity
            activity()
                ->performedOn($product)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldAttributes,
                    'attributes' => $product->toArray()
                ])
                ->log('Ürün güncellendi');
            
            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete a product
     */
    public function delete(Product $product)
    {
        DB::beginTransaction();
        
        try {
            // Delete images
            foreach ($product->images as $image) {
                $this->uploadService->delete($image);
            }
            
            // Delete featured image
            if ($product->featuredImage) {
                $this->uploadService->delete($product->featuredImage);
            }
            
            // Log activity
            activity()
                ->performedOn($product)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $product->toArray()])
                ->log('Ürün silindi');
            
            // Delete product
            $product->delete();
            
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
        $query = Product::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    /**
     * Sync product tags
     */
    protected function syncTags(Product $product, array $tags)
    {
        // This would typically use a tags table and pivot table
        // For now, we'll store as JSON
        $product->tags = json_encode($tags);
        $product->save();
    }

    /**
     * Duplicate a product
     */
    public function duplicate(Product $product)
    {
        DB::beginTransaction();
        
        try {
            $newProduct = $product->replicate();
            $newProduct->name = $product->name . ' (Copy)';
            $newProduct->slug = $this->generateUniqueSlug($newProduct->name);
            $newProduct->sku = $product->sku . '-COPY-' . uniqid();
            $newProduct->barcode = null; // Reset barcode
            $newProduct->status = 'draft';
            $newProduct->save();
            
            // Copy images
            foreach ($product->images as $image) {
                $newUpload = $this->uploadService->duplicate($image);
                $newUpload->relation_id = $newProduct->id;
                $newUpload->save();
            }
            
            // Copy featured image
            if ($product->featuredImage) {
                $newFeaturedImage = $this->uploadService->duplicate($product->featuredImage);
                $newProduct->featured_image_id = $newFeaturedImage->id;
                $newProduct->save();
            }
            
            DB::commit();
            return $newProduct;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update product stock
     */
    public function updateStock(Product $product, $quantity, $operation = 'set')
    {
        switch ($operation) {
            case 'increase':
                $product->quantity += $quantity;
                break;
            case 'decrease':
                $product->quantity = max(0, $product->quantity - $quantity);
                break;
            case 'set':
            default:
                $product->quantity = $quantity;
                break;
        }
        
        $product->save();
        
        // Log activity
        activity()
            ->performedOn($product)
            ->causedBy(auth()->user())
            ->withProperties([
                'operation' => $operation,
                'quantity' => $quantity,
                'new_stock' => $product->quantity
            ])
            ->log('Stok güncellendi');
        
        return $product;
    }

    /**
     * Get product statistics
     */
    public function getStatistics()
    {
        return [
            'total_products' => Product::count(),
            'active_products' => Product::where('status', 'active')->count(),
            'out_of_stock' => Product::where('quantity', 0)->count(),
            'low_stock' => Product::whereRaw('quantity <= min_quantity')->count(),
            'featured_products' => Product::where('is_featured', true)->count(),
            'total_value' => Product::sum(DB::raw('price * quantity')),
            'average_price' => Product::where('status', 'active')->avg('price'),
            'categories_used' => Product::distinct('category_id')->count('category_id'),
        ];
    }

    /**
     * Get related products
     */
    public function getRelatedProducts(Product $product, $limit = 4)
    {
        return Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Search products
     */
    public function search($query)
    {
        return Product::where('status', 'active')
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();
    }
}
