<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Products\Models\Product;
use App\Modules\Users\Models\User;
use App\Modules\Categories\Models\Category;
use App\Modules\Banners\Models\Banner;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_vendors' => User::role('vendor')->count(),
            'pending_products' => Product::pending()->count(),
            'active_banners' => Banner::active()->count(),
        ];

        $recentProducts = Product::with(['category', 'brand'])
            ->latest()
            ->take(5)
            ->get();

        $recentVendors = User::role('vendor')
            ->latest()
            ->take(5)
            ->get();

        $topCategories = Category::whereNull('parent_id')
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(4)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentProducts',
            'recentVendors',
            'topCategories'
        ));
    }
}
