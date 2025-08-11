<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        // Temporarily return a simple view until orders module is implemented
        $orders = collect([]); // Empty collection for now
        
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the specified order.
     */
    public function show($id)
    {
        // Placeholder for order details
        return view('admin.orders.show', ['order' => null]);
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, $id)
    {
        return redirect()->back()->with('info', 'Sipariş modülü henüz aktif değil.');
    }
}
