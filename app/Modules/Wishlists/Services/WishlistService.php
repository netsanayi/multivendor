<?php

namespace App\Modules\Wishlists\Services;

use App\Modules\Wishlists\Models\Wishlist;
use App\Modules\Products\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class WishlistService
{
    /**
     * Check for price drops and notify users.
     */
    public function checkPriceDrops(): void
    {
        $wishlists = Wishlist::with(['user', 'product'])
            ->notifiable()
            ->get();
        
        foreach ($wishlists as $wishlist) {
            if ($wishlist->isOnSale()) {
                $this->notifyPriceDrop($wishlist);
            }
        }
    }
    
    /**
     * Notify user about price drop.
     */
    public function notifyPriceDrop(Wishlist $wishlist): void
    {
        // Send email notification
        // Mail::to($wishlist->user)->send(new PriceDropNotification($wishlist));
        
        // Mark as notified
        $wishlist->markAsNotified();
        
        // Log activity
        activity()
            ->performedOn($wishlist)
            ->causedBy($wishlist->user)
            ->withProperties([
                'product_name' => $wishlist->product->name,
                'old_price' => $wishlist->price_when_added,
                'new_price' => $wishlist->product->price,
                'discount_percentage' => $wishlist->discount_percentage,
            ])
            ->log('Fiyat düşüşü bildirimi gönderildi');
    }
    
    /**
     * Generate share token for wishlist.
     */
    public function generateShareToken(int $userId): string
    {
        $token = Str::random(32);
        
        // Store in cache for 30 days
        Cache::put("wishlist_share_{$token}", $userId, now()->addDays(30));
        
        return $token;
    }
    
    /**
     * Get user ID from share token.
     */
    public function getUserIdFromShareToken(string $token): ?int
    {
        return Cache::get("wishlist_share_{$token}");
    }
    
    /**
     * Get recommended products based on wishlist.
     */
    public function getRecommendedProducts(int $userId, int $limit = 6): \Illuminate\Support\Collection
    {
        $wishlistProductIds = Wishlist::where('user_id', $userId)
            ->pluck('product_id')
            ->toArray();
        
        if (empty($wishlistProductIds)) {
            // Return random popular products if wishlist is empty
            return Product::where('status', 'active')
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }
        
        // Get categories from wishlist products
        $categoryIds = Product::whereIn('id', $wishlistProductIds)
            ->pluck('category_id')
            ->unique()
            ->toArray();
        
        // Get related products from same categories
        return Product::whereIn('category_id', $categoryIds)
            ->whereNotIn('id', $wishlistProductIds)
            ->where('status', 'active')
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
    
    /**
     * Import wishlist from another user (shared wishlist).
     */
    public function importWishlist(int $targetUserId, int $sourceUserId): array
    {
        $sourceWishlists = Wishlist::where('user_id', $sourceUserId)->get();
        
        $imported = 0;
        $skipped = 0;
        
        foreach ($sourceWishlists as $sourceWishlist) {
            // Check if product already in target user's wishlist
            if (Wishlist::userHasProduct($targetUserId, $sourceWishlist->product_id)) {
                $skipped++;
                continue;
            }
            
            // Add to target user's wishlist
            Wishlist::create([
                'user_id' => $targetUserId,
                'product_id' => $sourceWishlist->product_id,
                'priority' => $sourceWishlist->priority,
                'notes' => 'İçe aktarıldı: ' . $sourceWishlist->notes,
                'notify_on_sale' => $sourceWishlist->notify_on_sale,
            ]);
            
            $imported++;
        }
        
        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'total' => $sourceWishlists->count(),
        ];
    }
    
    /**
     * Export wishlist to various formats.
     */
    public function exportWishlist(int $userId, string $format = 'json'): string
    {
        $wishlists = Wishlist::with('product')
            ->where('user_id', $userId)
            ->get();
        
        switch ($format) {
            case 'json':
                return $this->exportToJson($wishlists);
                
            case 'csv':
                return $this->exportToCsv($wishlists);
                
            case 'pdf':
                return $this->exportToPdf($wishlists);
                
            default:
                return $this->exportToJson($wishlists);
        }
    }
    
    /**
     * Export wishlist to JSON.
     */
    private function exportToJson(\Illuminate\Database\Eloquent\Collection $wishlists): string
    {
        $data = $wishlists->map(function ($wishlist) {
            return [
                'product_name' => $wishlist->product->name,
                'product_sku' => $wishlist->product->sku,
                'current_price' => $wishlist->product->price,
                'price_when_added' => $wishlist->price_when_added,
                'discount' => $wishlist->discount_percentage,
                'priority' => $wishlist->priority,
                'notes' => $wishlist->notes,
                'added_at' => $wishlist->added_at->toDateTimeString(),
            ];
        });
        
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Export wishlist to CSV.
     */
    private function exportToCsv(\Illuminate\Database\Eloquent\Collection $wishlists): string
    {
        $csv = "Ürün Adı,SKU,Mevcut Fiyat,Eklendiğindeki Fiyat,İndirim %,Öncelik,Notlar,Eklenme Tarihi\n";
        
        foreach ($wishlists as $wishlist) {
            $csv .= sprintf(
                '"%s","%s",%.2f,%.2f,%.2f,%d,"%s","%s"' . "\n",
                $wishlist->product->name,
                $wishlist->product->sku,
                $wishlist->product->price,
                $wishlist->price_when_added,
                $wishlist->discount_percentage ?? 0,
                $wishlist->priority,
                $wishlist->notes ?? '',
                $wishlist->added_at->toDateTimeString()
            );
        }
        
        return $csv;
    }
    
    /**
     * Export wishlist to PDF (placeholder).
     */
    private function exportToPdf(\Illuminate\Database\Eloquent\Collection $wishlists): string
    {
        // This would require a PDF library like DomPDF or TCPDF
        // For now, return a simple HTML that can be converted to PDF
        
        $html = '<html><head><title>Favori Listem</title></head><body>';
        $html .= '<h1>Favori Ürünlerim</h1>';
        $html .= '<table border="1" cellpadding="5">';
        $html .= '<tr><th>Ürün</th><th>Fiyat</th><th>İndirim</th><th>Notlar</th></tr>';
        
        foreach ($wishlists as $wishlist) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($wishlist->product->name) . '</td>';
            $html .= '<td>₺' . number_format($wishlist->product->price, 2) . '</td>';
            $html .= '<td>' . ($wishlist->discount_percentage ?? 0) . '%</td>';
            $html .= '<td>' . htmlspecialchars($wishlist->notes ?? '') . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table></body></html>';
        
        return $html;
    }
    
    /**
     * Get wishlist statistics for user.
     */
    public function getUserStatistics(int $userId): array
    {
        $wishlists = Wishlist::with('product')
            ->where('user_id', $userId)
            ->get();
        
        $totalValue = $wishlists->sum(function ($item) {
            return $item->product ? $item->product->price : 0;
        });
        
        $originalValue = $wishlists->sum('price_when_added');
        
        $totalSavings = $wishlists->sum(function ($item) {
            return $item->discount_amount ?? 0;
        });
        
        $categoryCounts = [];
        foreach ($wishlists as $wishlist) {
            if ($wishlist->product && $wishlist->product->category) {
                $categoryName = $wishlist->product->category->name;
                $categoryCounts[$categoryName] = ($categoryCounts[$categoryName] ?? 0) + 1;
            }
        }
        
        arsort($categoryCounts);
        
        return [
            'total_items' => $wishlists->count(),
            'total_value' => $totalValue,
            'original_value' => $originalValue,
            'total_savings' => $totalSavings,
            'average_price' => $wishlists->count() > 0 ? $totalValue / $wishlists->count() : 0,
            'on_sale_count' => $wishlists->filter(fn($w) => $w->isOnSale())->count(),
            'top_categories' => array_slice($categoryCounts, 0, 5, true),
            'oldest_item' => $wishlists->min('added_at'),
            'newest_item' => $wishlists->max('added_at'),
        ];
    }
    
    /**
     * Clean up old wishlist items.
     */
    public function cleanupOldItems(int $daysOld = 365): int
    {
        return Wishlist::where('added_at', '<', now()->subDays($daysOld))
            ->whereHas('product', function ($query) {
                $query->where('status', '!=', 'active');
            })
            ->delete();
    }
    
    /**
     * Merge duplicate wishlist items.
     */
    public function mergeDuplicates(int $userId): int
    {
        $duplicates = Wishlist::select('product_id')
            ->where('user_id', $userId)
            ->groupBy('product_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('product_id');
        
        $merged = 0;
        
        foreach ($duplicates as $productId) {
            $items = Wishlist::where('user_id', $userId)
                ->where('product_id', $productId)
                ->orderBy('added_at')
                ->get();
            
            // Keep the first one, delete others
            $first = $items->shift();
            
            foreach ($items as $item) {
                // Merge notes
                if ($item->notes && $first->notes !== $item->notes) {
                    $first->notes .= "\n" . $item->notes;
                    $first->save();
                }
                
                $item->delete();
                $merged++;
            }
        }
        
        return $merged;
    }
}
