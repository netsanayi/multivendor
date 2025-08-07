<?php

namespace App\Http\Controllers;

use App\Modules\Products\Models\Product;
use App\Modules\Categories\Models\Category;
use App\Modules\Banners\Models\Banner;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $featuredProducts = Product::with(['category', 'brand', 'vendorProducts'])
            ->where('status', true)
            ->where('approval_status', 'approved')
            ->inRandomOrder()
            ->take(8)
            ->get();

        $latestProducts = Product::with(['category', 'brand', 'vendorProducts'])
            ->where('status', true)
            ->where('approval_status', 'approved')
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::whereNull('parent_id')
            ->where('status', true)
            ->orderBy('order')
            ->take(6)
            ->get();

        $banners = Banner::where('status', true)
            ->orderBy('order')
            ->get();

        return view('welcome', compact(
            'featuredProducts',
            'latestProducts',
            'categories',
            'banners'
        ));
    }

    /**
     * Display the about page.
     *
     * @return \Illuminate\View\View
     */
    public function about()
    {
        return view('pages.about');
    }

    /**
     * Display the contact page.
     *
     * @return \Illuminate\View\View
     */
    public function contact()
    {
        return view('pages.contact');
    }

    /**
     * Handle contact form submission.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // TODO: Send email or save to database

        return redirect()->route('contact')->with('success', 'Mesajınız başarıyla gönderildi!');
    }
}
