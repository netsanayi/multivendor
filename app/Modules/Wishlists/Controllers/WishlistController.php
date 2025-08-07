<?php

namespace App\Modules\Wishlists\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Wishlists\Models\Wishlist;
use App\Modules\Wishlists\Services\WishlistService;
use App\Modules\Products\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    protected $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
        $this->middleware('auth');
    }

    /**
     * Display user's wishlist.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Wishlist::with(['product', 'product.category', 'product.images'])
            ->forUser($user->id);
        
        // Sort by
        $sortBy = $request->get('sort', 'priority');
        switch ($sortBy) {
            case 'name':
                $query->join('products', 'wishlists.product_id', '=', 'products.id')
                    ->orderBy('products.name')
                    ->select('wishlists.*');
                break;
            case 'price_low':
                $query->join('products', 'wishlists.product_id', '=', 'products.id')
                    ->orderBy('products.price')
                    ->select('wishlists.*');
                break;
            case 'price_high':
                $query->join('products', 'wishlists.product_id', '=', 'products.id')
                    ->orderBy('products.price', 'desc')
                    ->select('wishlists.*');
                break;
            case 'date':
                $query->latest('added_at');
                break;
            default:
                $query->byPriority();
        }
        
        $wishlists = $query->paginate(12);
        
        // Get summary
        $summary = Wishlist::getUserSummary($user->id);
        
        return view('wishlists.index', compact('wishlists', 'summary', 'sortBy'));
    }

    /**
     * Add product to wishlist.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'priority' => 'nullable|integer|min:0|max:10',
            'notes' => 'nullable|string|max:500',
            'notify_on_sale' => 'nullable|boolean',
        ]);
        
        $user = auth()->user();
        $product = Product::findOrFail($validated['product_id']);
        
        // Check if product is already in wishlist
        if (Wishlist::userHasProduct($user->id, $product->id)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu ürün zaten favorilerinizde.',
                ], 422);
            }
            
            return redirect()
                ->back()
                ->with('warning', 'Bu ürün zaten favorilerinizde.');
        }
        
        DB::beginTransaction();
        try {
            $wishlist = Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'priority' => $validated['priority'] ?? 0,
                'notes' => $validated['notes'] ?? null,
                'notify_on_sale' => $validated['notify_on_sale'] ?? true,
                'price_when_added' => $product->price,
            ]);
            
            // Log activity
            activity()
                ->performedOn($wishlist)
                ->causedBy($user)
                ->withProperties(['product_name' => $product->name])
                ->log('Ürün favorilere eklendi');
            
            DB::commit();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ürün favorilerinize eklendi.',
                    'wishlist' => $wishlist,
                    'summary' => Wishlist::getUserSummary($user->id),
                ]);
            }
            
            return redirect()
                ->back()
                ->with('success', 'Ürün favorilerinize eklendi.');
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ürün eklenirken bir hata oluştu.',
                ], 500);
            }
            
            return redirect()
                ->back()
                ->with('error', 'Ürün eklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Toggle product in wishlist (AJAX).
     */
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
        
        $user = auth()->user();
        $product = Product::findOrFail($validated['product_id']);
        
        DB::beginTransaction();
        try {
            $result = Wishlist::toggleProduct($user->id, $product->id);
            
            // Log activity
            $action = $result['action'] == 'added' ? 'eklendi' : 'çıkarıldı';
            activity()
                ->causedBy($user)
                ->withProperties(['product_name' => $product->name, 'action' => $result['action']])
                ->log("Ürün favorilerden {$action}");
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'action' => $result['action'],
                'message' => $result['action'] == 'added' 
                    ? 'Ürün favorilerinize eklendi.' 
                    : 'Ürün favorilerinizden çıkarıldı.',
                'wishlist' => $result['wishlist'],
                'summary' => Wishlist::getUserSummary($user->id),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'İşlem sırasında bir hata oluştu.',
            ], 500);
        }
    }

    /**
     * Update wishlist item.
     */
    public function update(Request $request, Wishlist $wishlist)
    {
        // Check ownership
        if ($wishlist->user_id !== auth()->id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'priority' => 'nullable|integer|min:0|max:10',
            'notes' => 'nullable|string|max:500',
            'notify_on_sale' => 'nullable|boolean',
        ]);
        
        $wishlist->update($validated);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Favori güncellendi.',
                'wishlist' => $wishlist,
            ]);
        }
        
        return redirect()
            ->back()
            ->with('success', 'Favori güncellendi.');
    }

    /**
     * Remove product from wishlist.
     */
    public function destroy(Request $request, Wishlist $wishlist)
    {
        // Check ownership
        if ($wishlist->user_id !== auth()->id()) {
            abort(403);
        }
        
        $productName = $wishlist->product->name;
        
        // Log activity
        activity()
            ->performedOn($wishlist)
            ->causedBy(auth()->user())
            ->withProperties(['product_name' => $productName])
            ->log('Ürün favorilerden çıkarıldı');
        
        $wishlist->delete();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ürün favorilerinizden çıkarıldı.',
                'summary' => Wishlist::getUserSummary(auth()->id()),
            ]);
        }
        
        return redirect()
            ->back()
            ->with('success', 'Ürün favorilerinizden çıkarıldı.');
    }

    /**
     * Clear all wishlist items.
     */
    public function clear(Request $request)
    {
        $user = auth()->user();
        
        DB::beginTransaction();
        try {
            $count = Wishlist::where('user_id', $user->id)->count();
            
            Wishlist::where('user_id', $user->id)->delete();
            
            // Log activity
            activity()
                ->causedBy($user)
                ->withProperties(['items_count' => $count])
                ->log('Tüm favoriler temizlendi');
            
            DB::commit();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tüm favorileriniz temizlendi.',
                ]);
            }
            
            return redirect()
                ->route('wishlist.index')
                ->with('success', 'Tüm favorileriniz temizlendi.');
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'İşlem sırasında bir hata oluştu.',
                ], 500);
            }
            
            return redirect()
                ->back()
                ->with('error', 'İşlem sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Add all wishlist items to cart.
     */
    public function addAllToCart(Request $request)
    {
        $user = auth()->user();
        
        $wishlists = Wishlist::with('product')
            ->forUser($user->id)
            ->get();
        
        if ($wishlists->isEmpty()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Favorilerinizde ürün bulunmuyor.',
                ], 422);
            }
            
            return redirect()
                ->back()
                ->with('warning', 'Favorilerinizde ürün bulunmuyor.');
        }
        
        DB::beginTransaction();
        try {
            $addedCount = 0;
            $skippedCount = 0;
            
            foreach ($wishlists as $wishlist) {
                if ($wishlist->product && $wishlist->product->isAvailable()) {
                    // This will be implemented when Cart module is created
                    // Cart::add($wishlist->product_id, 1);
                    $addedCount++;
                } else {
                    $skippedCount++;
                }
            }
            
            DB::commit();
            
            $message = "{$addedCount} ürün sepete eklendi.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} ürün stokta olmadığı için eklenemedi.";
            }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'added_count' => $addedCount,
                    'skipped_count' => $skippedCount,
                ]);
            }
            
            return redirect()
                ->route('cart.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'İşlem sırasında bir hata oluştu.',
                ], 500);
            }
            
            return redirect()
                ->back()
                ->with('error', 'İşlem sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Share wishlist.
     */
    public function share(Request $request)
    {
        $user = auth()->user();
        
        // Generate shareable link
        $shareToken = $this->wishlistService->generateShareToken($user->id);
        
        $shareUrl = route('wishlist.shared', ['token' => $shareToken]);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'share_url' => $shareUrl,
                'share_token' => $shareToken,
            ]);
        }
        
        return view('wishlists.share', compact('shareUrl', 'shareToken'));
    }

    /**
     * View shared wishlist.
     */
    public function shared(Request $request, $token)
    {
        $userId = $this->wishlistService->getUserIdFromShareToken($token);
        
        if (!$userId) {
            abort(404, 'Paylaşılan favori listesi bulunamadı.');
        }
        
        $user = \App\Models\User::findOrFail($userId);
        
        $wishlists = Wishlist::with(['product', 'product.category', 'product.images'])
            ->forUser($userId)
            ->byPriority()
            ->paginate(12);
        
        $summary = Wishlist::getUserSummary($userId);
        
        return view('wishlists.shared', compact('wishlists', 'summary', 'user'));
    }
}
