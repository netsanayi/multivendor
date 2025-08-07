<?php

namespace App\Modules\VendorDashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\VendorDashboard\Models\VendorCommission;
use App\Modules\VendorDashboard\Models\VendorEarning;
use App\Modules\VendorDashboard\Models\VendorPayout;
use App\Modules\VendorDashboard\Services\VendorDashboardService;
use App\Modules\Products\Models\Product;
use App\Modules\VendorProducts\Models\VendorProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VendorDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(VendorDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
        
        // Middleware to ensure user is a vendor
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->hasRole('vendor')) {
                abort(403, 'Unauthorized access.');
            }
            return $next($request);
        });
    }

    /**
     * Display vendor dashboard.
     */
    public function index(Request $request)
    {
        $vendor = auth()->user();
        $period = $request->get('period', 'month'); // today, week, month, year
        
        // Get statistics
        $stats = $this->dashboardService->getVendorStatistics($vendor->id, $period);
        
        // Get recent orders
        $recentOrders = $this->dashboardService->getRecentOrders($vendor->id, 10);
        
        // Get top products
        $topProducts = $this->dashboardService->getTopProducts($vendor->id, 5);
        
        // Get earnings chart data
        $chartData = $this->dashboardService->getEarningsChartData($vendor->id, $period);
        
        // Get pending payouts
        $pendingPayouts = VendorPayout::where('vendor_id', $vendor->id)
            ->whereIn('status', ['pending', 'processing'])
            ->latest()
            ->take(5)
            ->get();

        return view('vendor.dashboard.index', compact(
            'stats',
            'recentOrders',
            'topProducts',
            'chartData',
            'pendingPayouts',
            'period'
        ));
    }

    /**
     * Display earnings page.
     */
    public function earnings(Request $request)
    {
        $vendor = auth()->user();
        
        $query = VendorEarning::where('vendor_id', $vendor->id);
        
        // Date filter
        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->get('date_from'));
        }
        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->get('date_to'));
        }
        
        // Status filter
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }
        
        $earnings = $query->latest()->paginate(20);
        
        // Calculate summary
        $summary = [
            'total_gross' => $query->sum('gross_amount'),
            'total_commission' => $query->sum('commission_amount'),
            'total_net' => $query->sum('net_amount'),
            'pending_amount' => $query->clone()->pending()->sum('net_amount'),
            'approved_amount' => $query->clone()->approved()->sum('net_amount'),
            'paid_amount' => $query->clone()->paid()->sum('net_amount'),
        ];

        return view('vendor.dashboard.earnings', compact('earnings', 'summary'));
    }

    /**
     * Display payouts page.
     */
    public function payouts(Request $request)
    {
        $vendor = auth()->user();
        
        $query = VendorPayout::where('vendor_id', $vendor->id);
        
        // Status filter
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }
        
        $payouts = $query->latest()->paginate(20);
        
        // Get available balance
        $availableBalance = VendorEarning::where('vendor_id', $vendor->id)
            ->whereIn('status', ['approved'])
            ->sum('net_amount');

        return view('vendor.dashboard.payouts', compact('payouts', 'availableBalance'));
    }

    /**
     * Request a new payout.
     */
    public function requestPayout(Request $request)
    {
        $vendor = auth()->user();
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:10',
            'payment_method' => 'required|in:bank_transfer,paypal,stripe',
            'bank_details' => 'required_if:payment_method,bank_transfer|array',
            'notes' => 'nullable|string|max:500',
        ]);
        
        // Check available balance
        $availableBalance = VendorEarning::where('vendor_id', $vendor->id)
            ->whereIn('status', ['approved'])
            ->sum('net_amount');
        
        if ($validated['amount'] > $availableBalance) {
            return redirect()
                ->back()
                ->with('error', 'Yetersiz bakiye. Kullanılabilir bakiye: ₺' . number_format($availableBalance, 2));
        }
        
        DB::beginTransaction();
        try {
            // Get earnings to be included in payout
            $earnings = VendorEarning::where('vendor_id', $vendor->id)
                ->whereIn('status', ['approved'])
                ->orderBy('created_at')
                ->get();
            
            $totalAmount = 0;
            $earningIds = [];
            
            foreach ($earnings as $earning) {
                if ($totalAmount + $earning->net_amount <= $validated['amount']) {
                    $totalAmount += $earning->net_amount;
                    $earningIds[] = $earning->id;
                } else {
                    break;
                }
            }
            
            // Create payout request
            $payout = VendorPayout::create([
                'vendor_id' => $vendor->id,
                'amount' => $totalAmount,
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'bank_details' => $validated['bank_details'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'earnings_ids' => $earningIds,
            ]);
            
            // Log activity
            activity()
                ->performedOn($payout)
                ->causedBy($vendor)
                ->withProperties(['amount' => $totalAmount])
                ->log('Ödeme talebi oluşturuldu');
            
            DB::commit();
            
            return redirect()
                ->route('vendor.dashboard.payouts')
                ->with('success', 'Ödeme talebiniz alındı. Payout #' . $payout->payout_number);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Ödeme talebi oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Display products page.
     */
    public function products(Request $request)
    {
        $vendor = auth()->user();
        
        $query = VendorProduct::where('vendor_id', $vendor->id)
            ->with(['product', 'product.category']);
        
        // Search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }
        
        // Stock filter
        if ($request->has('stock_status')) {
            if ($request->get('stock_status') == 'out_of_stock') {
                $query->where('stock_quantity', 0);
            } elseif ($request->get('stock_status') == 'low_stock') {
                $query->whereBetween('stock_quantity', [1, 10]);
            }
        }
        
        $products = $query->latest()->paginate(20);

        return view('vendor.dashboard.products', compact('products'));
    }

    /**
     * Display orders page.
     */
    public function orders(Request $request)
    {
        $vendor = auth()->user();
        
        // This will be implemented when Orders module is created
        $orders = collect(); // Temporary empty collection
        
        return view('vendor.dashboard.orders', compact('orders'));
    }

    /**
     * Display analytics page.
     */
    public function analytics(Request $request)
    {
        $vendor = auth()->user();
        $period = $request->get('period', 'month');
        
        // Get various analytics data
        $salesAnalytics = $this->dashboardService->getSalesAnalytics($vendor->id, $period);
        $productPerformance = $this->dashboardService->getProductPerformance($vendor->id, $period);
        $customerAnalytics = $this->dashboardService->getCustomerAnalytics($vendor->id, $period);
        
        return view('vendor.dashboard.analytics', compact(
            'salesAnalytics',
            'productPerformance',
            'customerAnalytics',
            'period'
        ));
    }

    /**
     * Display settings page.
     */
    public function settings()
    {
        $vendor = auth()->user();
        
        // Get vendor commission settings
        $commission = VendorCommission::where('vendor_id', $vendor->id)
            ->active()
            ->first();
        
        return view('vendor.dashboard.settings', compact('vendor', 'commission'));
    }

    /**
     * Update vendor settings.
     */
    public function updateSettings(Request $request)
    {
        $vendor = auth()->user();
        
        $validated = $request->validate([
            'shop_name' => 'nullable|string|max:255',
            'shop_description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_iban' => 'nullable|string|max:50',
        ]);
        
        // Update vendor metadata
        $vendor->update([
            'metadata' => array_merge($vendor->metadata ?? [], $validated)
        ]);
        
        // Log activity
        activity()
            ->performedOn($vendor)
            ->causedBy($vendor)
            ->log('Vendor ayarları güncellendi');
        
        return redirect()
            ->back()
            ->with('success', 'Ayarlarınız başarıyla güncellendi.');
    }
}
