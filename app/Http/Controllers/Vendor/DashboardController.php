<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Modules\VendorProducts\Models\VendorProduct;
use App\Modules\Products\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the vendor dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $vendorId = Auth::id();

        $stats = [
            'total_products' => VendorProduct::where('user_relation_id', $vendorId)->count(),
            'active_products' => VendorProduct::where('user_relation_id', $vendorId)
                ->where('status', true)
                ->count(),
            'out_of_stock' => VendorProduct::where('user_relation_id', $vendorId)
                ->where('stock_quantity', 0)
                ->count(),
            'pending_approval' => VendorProduct::where('user_relation_id', $vendorId)
                ->whereHas('product', function ($query) {
                    $query->where('approval_status', 'pending');
                })
                ->count(),
        ];

        $recentProducts = VendorProduct::with(['product.category', 'product.brand'])
            ->where('user_relation_id', $vendorId)
            ->latest()
            ->take(10)
            ->get();

        $lowStockProducts = VendorProduct::with(['product'])
            ->where('user_relation_id', $vendorId)
            ->where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', 10)
            ->orderBy('stock_quantity')
            ->take(5)
            ->get();

        return view('vendor.dashboard', compact(
            'stats',
            'recentProducts',
            'lowStockProducts'
        ));
    }
}
