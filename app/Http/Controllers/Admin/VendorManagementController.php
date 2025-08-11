<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Users\Models\User;
use App\Modules\VendorProducts\Models\VendorProduct;
use App\Modules\VendorDashboard\Models\VendorEarning;
use App\Modules\VendorDashboard\Models\VendorPayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorManagementController extends Controller
{
    /**
     * Display a listing of vendors.
     */
    public function index(Request $request)
    {
        $query = User::whereHas('roles', function($q) {
            $q->where('name', 'vendor');
        })->with(['vendorProducts']);

        // Search filter
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status')) {
            $query->where('is_active', $request->get('status'));
        }

        // Sort
        $query->orderBy('created_at', 'desc');

        $vendors = $query->paginate(20);

        // Get statistics for each vendor
        $vendors->each(function($vendor) {
            $vendor->statistics = $this->getVendorStatistics($vendor->id);
        });

        return view('admin.vendors.index', compact('vendors'));
    }

    /**
     * Show vendor applications (pending approval).
     */
    public function applications(Request $request)
    {
        $query = User::whereHas('roles', function($q) {
            $q->where('name', 'vendor');
        })->where('vendor_status', 'pending');

        // Search filter
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.vendors.applications', compact('applications'));
    }

    /**
     * Show vendor details.
     */
    public function show(User $vendor)
    {
        // Ensure the user is a vendor
        if (!$vendor->hasRole('vendor')) {
            abort(404);
        }

        $statistics = $this->getVendorStatistics($vendor->id);
        $products = VendorProduct::where('vendor_id', $vendor->id)
            ->with(['product', 'currency'])
            ->paginate(10);
        $earnings = VendorEarning::where('vendor_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        $payouts = VendorPayout::where('vendor_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.vendors.show', compact('vendor', 'statistics', 'products', 'earnings', 'payouts'));
    }

    /**
     * Approve vendor application.
     */
    public function approve(User $vendor)
    {
        // Ensure the user is a vendor
        if (!$vendor->hasRole('vendor')) {
            abort(404);
        }

        $vendor->update([
            'vendor_status' => 'approved',
            'vendor_approved_at' => now(),
            'vendor_approved_by' => auth()->id()
        ]);

        // Send notification to vendor
        // $vendor->notify(new VendorApprovedNotification());

        return redirect()->back()->with('success', 'Satıcı başvurusu onaylandı.');
    }

    /**
     * Reject vendor application.
     */
    public function reject(Request $request, User $vendor)
    {
        // Ensure the user is a vendor
        if (!$vendor->hasRole('vendor')) {
            abort(404);
        }

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $vendor->update([
            'vendor_status' => 'rejected',
            'vendor_rejected_at' => now(),
            'vendor_rejected_by' => auth()->id(),
            'vendor_rejection_reason' => $request->reason
        ]);

        // Send notification to vendor
        // $vendor->notify(new VendorRejectedNotification($request->reason));

        return redirect()->back()->with('success', 'Satıcı başvurusu reddedildi.');
    }

    /**
     * Suspend vendor account.
     */
    public function suspend(Request $request, User $vendor)
    {
        // Ensure the user is a vendor
        if (!$vendor->hasRole('vendor')) {
            abort(404);
        }

        $request->validate([
            'reason' => 'required|string|max:500',
            'duration' => 'nullable|integer|min:1'
        ]);

        $vendor->update([
            'vendor_status' => 'suspended',
            'vendor_suspended_at' => now(),
            'vendor_suspended_by' => auth()->id(),
            'vendor_suspension_reason' => $request->reason,
            'vendor_suspension_ends_at' => $request->duration ? now()->addDays($request->duration) : null
        ]);

        // Deactivate all vendor products
        VendorProduct::where('vendor_id', $vendor->id)
            ->update(['is_active' => false]);

        // Send notification to vendor
        // $vendor->notify(new VendorSuspendedNotification($request->reason));

        return redirect()->back()->with('success', 'Satıcı hesabı askıya alındı.');
    }

    /**
     * Activate vendor account.
     */
    public function activate(User $vendor)
    {
        // Ensure the user is a vendor
        if (!$vendor->hasRole('vendor')) {
            abort(404);
        }

        $vendor->update([
            'vendor_status' => 'approved',
            'vendor_suspended_at' => null,
            'vendor_suspended_by' => null,
            'vendor_suspension_reason' => null,
            'vendor_suspension_ends_at' => null
        ]);

        // Reactivate vendor products
        VendorProduct::where('vendor_id', $vendor->id)
            ->update(['is_active' => true]);

        // Send notification to vendor
        // $vendor->notify(new VendorActivatedNotification());

        return redirect()->back()->with('success', 'Satıcı hesabı aktifleştirildi.');
    }

    /**
     * Update vendor commission rate.
     */
    public function updateCommission(Request $request, User $vendor)
    {
        // Ensure the user is a vendor
        if (!$vendor->hasRole('vendor')) {
            abort(404);
        }

        $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
            'commission_type' => 'required|in:percentage,fixed'
        ]);

        $vendor->update([
            'vendor_commission_rate' => $request->commission_rate,
            'vendor_commission_type' => $request->commission_type
        ]);

        return redirect()->back()->with('success', 'Komisyon oranı güncellendi.');
    }

    /**
     * Get vendor statistics.
     */
    private function getVendorStatistics($vendorId)
    {
        return [
            'total_products' => VendorProduct::where('vendor_id', $vendorId)->count(),
            'active_products' => VendorProduct::where('vendor_id', $vendorId)->where('is_active', true)->count(),
            'total_orders' => DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('vendor_products', 'order_items.vendor_product_id', '=', 'vendor_products.id')
                ->where('vendor_products.vendor_id', $vendorId)
                ->distinct('orders.id')
                ->count('orders.id'),
            'total_earnings' => VendorEarning::where('vendor_id', $vendorId)->sum('amount'),
            'pending_earnings' => VendorEarning::where('vendor_id', $vendorId)->where('status', 'pending')->sum('amount'),
            'total_payouts' => VendorPayout::where('vendor_id', $vendorId)->where('status', 'completed')->sum('amount'),
            'pending_payouts' => VendorPayout::where('vendor_id', $vendorId)->where('status', 'pending')->sum('amount'),
            'average_rating' => DB::table('vendor_reviews')->where('vendor_id', $vendorId)->avg('rating') ?? 0,
            'total_reviews' => DB::table('vendor_reviews')->where('vendor_id', $vendorId)->count()
        ];
    }
}
