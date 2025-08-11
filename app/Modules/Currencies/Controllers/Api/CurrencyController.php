<?php

namespace App\Modules\Currencies\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Currencies\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Currency::active()
            ->orderBy('name');

        $currencies = $query->get();

        return response()->json($currencies);
    }

    /**
     * Display the specified resource.
     */
    public function show(Currency $currency)
    {
        if (!$currency->status) {
            return response()->json(['message' => 'Currency not found'], 404);
        }
        
        return response()->json($currency);
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
    public function update(Request $request, Currency $currency)
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Currency $currency)
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
