<?php

namespace App\Modules\Brands\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Brands\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Brand::with(['image'])
            ->active()
            ->orderBy('order')
            ->orderBy('name');

        $brands = $query->get();

        return response()->json($brands);
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        if (!$brand->status) {
            return response()->json(['message' => 'Brand not found'], 404);
        }

        $brand->load(['image']);
        
        return response()->json($brand);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
