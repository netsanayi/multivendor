<?php

namespace App\Modules\VendorProducts\Services;

use App\Modules\VendorProducts\Models\VendorProduct;
use App\Modules\Products\Models\Product;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class VendorProductService
{
    /**
     * Get all vendor products with filters
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllVendorProducts(array $filters = [], int $perPage = 20)
    {
        $query = VendorProduct::with(['product', 'vendor', 'currency']);

        // Apply filters
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if (isset($filters['vendor_id'])) {
            $query->where('vendor_id', $filters['vendor_id']);
        }

        if (isset($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        if (isset($filters['condition'])) {
            $query->where('condition', $filters['condition']);
        }

        if (isset($filters['availability'])) {
            $query->where('availability', $filters['availability']);
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (isset($filters['in_stock'])) {
            if ($filters['in_stock']) {
                $query->where('quantity', '>', 0);
            } else {
                $query->where('quantity', '=', 0);
            }
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get vendor products by vendor
     *
     * @param int $vendorId
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getVendorProducts(int $vendorId, array $filters = [], int $perPage = 20)
    {
        $filters['vendor_id'] = $vendorId;
        return $this->getAllVendorProducts($filters, $perPage);
    }

    /**
     * Get active vendor products for a product
     *
     * @param int $productId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveVendorProductsForProduct(int $productId)
    {
        return Cache::remember("vendor_products_for_{$productId}", 3600, function () use ($productId) {
            return VendorProduct::with(['vendor', 'currency'])
                ->where('product_id', $productId)
                ->where('is_active', true)
                ->where('quantity', '>', 0)
                ->orderBy('price', 'asc')
                ->get();
        });
    }

    /**
     * Create a new vendor product
     *
     * @param array $data
     * @return VendorProduct
     */
    public function createVendorProduct(array $data): VendorProduct
    {
        return DB::transaction(function () use ($data) {
            // Check if vendor already has this product
            $exists = VendorProduct::where('vendor_id', $data['vendor_id'])
                ->where('product_id', $data['product_id'])
                ->exists();

            if ($exists) {
                throw new \Exception('Bu satıcı için bu ürün zaten tanımlanmış.');
            }

            // Set default values
            $data['is_active'] = $data['is_active'] ?? true;
            $data['track_inventory'] = $data['track_inventory'] ?? true;
            $data['commission_type'] = $data['commission_type'] ?? 'percentage';

            // Create vendor product
            $vendorProduct = VendorProduct::create($data);

            // Clear cache
            $this->clearCache($vendorProduct);

            // Log activity
            activity()
                ->performedOn($vendorProduct)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $vendorProduct->toArray()])
                ->log('Satıcı ürünü oluşturuldu');

            return $vendorProduct;
        });
    }

    /**
     * Update a vendor product
     *
     * @param VendorProduct $vendorProduct
     * @param array $data
     * @return VendorProduct
     */
    public function updateVendorProduct(VendorProduct $vendorProduct, array $data): VendorProduct
    {
        return DB::transaction(function () use ($vendorProduct, $data) {
            $oldAttributes = $vendorProduct->toArray();

            // Update vendor product
            $vendorProduct->update($data);

            // Clear cache
            $this->clearCache($vendorProduct);

            // Log activity
            activity()
                ->performedOn($vendorProduct)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldAttributes,
                    'attributes' => $vendorProduct->toArray()
                ])
                ->log('Satıcı ürünü güncellendi');

            return $vendorProduct->fresh();
        });
    }

    /**
     * Delete a vendor product
     *
     * @param VendorProduct $vendorProduct
     * @return bool
     */
    public function deleteVendorProduct(VendorProduct $vendorProduct): bool
    {
        return DB::transaction(function () use ($vendorProduct) {
            // Check if product has orders
            if ($this->hasOrders($vendorProduct)) {
                throw new \Exception('Bu ürün sipariş geçmişine sahip olduğu için silinemez.');
            }

            // Log activity
            activity()
                ->performedOn($vendorProduct)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $vendorProduct->toArray()])
                ->log('Satıcı ürünü silindi');

            // Clear cache
            $this->clearCache($vendorProduct);

            // Delete vendor product
            return $vendorProduct->delete();
        });
    }

    /**
     * Toggle vendor product active status
     *
     * @param VendorProduct $vendorProduct
     * @return VendorProduct
     */
    public function toggleActive(VendorProduct $vendorProduct): VendorProduct
    {
        $vendorProduct->update(['is_active' => !$vendorProduct->is_active]);
        $this->clearCache($vendorProduct);
        return $vendorProduct;
    }

    /**
     * Toggle vendor product featured status
     *
     * @param VendorProduct $vendorProduct
     * @return VendorProduct
     */
    public function toggleFeatured(VendorProduct $vendorProduct): VendorProduct
    {
        $vendorProduct->update(['is_featured' => !$vendorProduct->is_featured]);
        $this->clearCache($vendorProduct);
        return $vendorProduct;
    }

    /**
     * Update stock quantity
     *
     * @param VendorProduct $vendorProduct
     * @param int $quantity
     * @param string $operation
     * @return VendorProduct
     */
    public function updateStock(VendorProduct $vendorProduct, int $quantity, string $operation = 'set'): VendorProduct
    {
        return DB::transaction(function () use ($vendorProduct, $quantity, $operation) {
            $oldQuantity = $vendorProduct->quantity;

            switch ($operation) {
                case 'increment':
                    $newQuantity = $oldQuantity + $quantity;
                    break;
                case 'decrement':
                    $newQuantity = max(0, $oldQuantity - $quantity);
                    break;
                default:
                    $newQuantity = max(0, $quantity);
            }

            $vendorProduct->update(['quantity' => $newQuantity]);

            // Update availability
            if ($newQuantity === 0 && $vendorProduct->availability === 'in_stock') {
                $vendorProduct->update(['availability' => 'out_of_stock']);
            } elseif ($newQuantity > 0 && $vendorProduct->availability === 'out_of_stock') {
                $vendorProduct->update(['availability' => 'in_stock']);
            }

            // Log stock change
            activity()
                ->performedOn($vendorProduct)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old_quantity' => $oldQuantity,
                    'new_quantity' => $newQuantity,
                    'operation' => $operation
                ])
                ->log('Stok güncellendi');

            $this->clearCache($vendorProduct);

            return $vendorProduct->fresh();
        });
    }

    /**
     * Get vendor product statistics
     *
     * @param int|null $vendorId
     * @return array
     */
    public function getStatistics(?int $vendorId = null): array
    {
        $query = VendorProduct::query();
        
        if ($vendorId) {
            $query->where('vendor_id', $vendorId);
        }

        return Cache::remember("vendor_product_statistics_{$vendorId}", 3600, function () use ($query) {
            $baseQuery = clone $query;
            
            return [
                'total' => $baseQuery->count(),
                'active' => (clone $query)->where('is_active', true)->count(),
                'inactive' => (clone $query)->where('is_active', false)->count(),
                'featured' => (clone $query)->where('is_featured', true)->count(),
                'in_stock' => (clone $query)->where('quantity', '>', 0)->count(),
                'out_of_stock' => (clone $query)->where('quantity', '=', 0)->count(),
                'low_stock' => (clone $query)->whereBetween('quantity', [1, 10])->count(),
                'total_stock_value' => (clone $query)->sum(DB::raw('price * quantity')),
                'average_price' => (clone $query)->avg('price'),
                'conditions' => [
                    'new' => (clone $query)->where('condition', 'new')->count(),
                    'used' => (clone $query)->where('condition', 'used')->count(),
                    'refurbished' => (clone $query)->where('condition', 'refurbished')->count()
                ]
            ];
        });
    }

    /**
     * Get best selling vendor products
     *
     * @param int|null $vendorId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBestSellingProducts(?int $vendorId = null, int $limit = 10)
    {
        $query = VendorProduct::with(['product', 'vendor'])
            ->select('vendor_products.*')
            ->selectRaw('COUNT(order_items.id) as sales_count')
            ->leftJoin('order_items', 'vendor_products.id', '=', 'order_items.vendor_product_id')
            ->groupBy('vendor_products.id')
            ->orderBy('sales_count', 'desc');

        if ($vendorId) {
            $query->where('vendor_products.vendor_id', $vendorId);
        }

        return $query->limit($limit)->get();
    }

    /**
     * Check if vendor product has orders
     *
     * @param VendorProduct $vendorProduct
     * @return bool
     */
    private function hasOrders(VendorProduct $vendorProduct): bool
    {
        return DB::table('order_items')
            ->where('vendor_product_id', $vendorProduct->id)
            ->exists();
    }

    /**
     * Clear vendor product cache
     *
     * @param VendorProduct|null $vendorProduct
     * @return void
     */
    private function clearCache(?VendorProduct $vendorProduct = null): void
    {
        if ($vendorProduct) {
            Cache::forget("vendor_products_for_{$vendorProduct->product_id}");
            Cache::forget("vendor_product_statistics_{$vendorProduct->vendor_id}");
        }
        
        Cache::forget('vendor_product_statistics_');
    }
}
