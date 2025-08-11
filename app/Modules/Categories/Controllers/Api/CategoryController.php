<?php

namespace App\Modules\Categories\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Categories\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::with(['parent', 'children', 'image'])
            ->active()
            ->orderBy('order')
            ->orderBy('name');

        $categories = $query->get();

        return response()->json($categories);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        if (!$category->status) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->load(['parent', 'children', 'image']);
        
        return response()->json($category);
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
    public function update(Request $request, Category $category)
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
