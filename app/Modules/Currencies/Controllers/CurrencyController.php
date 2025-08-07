<?php

namespace App\Modules\Currencies\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Currencies\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Currency::query();

        // Arama
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('symbol', 'like', "%{$search}%");
        }

        // Durum filtresi
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Sıralama
        $query->orderBy('name', 'asc');

        $currencies = $query->paginate(20);

        return view('currencies.index', compact('currencies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('currencies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:currencies,name',
            'symbol' => 'required|string|max:10',
            'position' => 'required|in:left,right',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $currency = Currency::create($validated);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($currency)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $currency->toArray()])
                ->log('Para birimi oluşturuldu');

            DB::commit();

            return redirect()
                ->route('admin.currencies.index')
                ->with('success', 'Para birimi başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Para birimi oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Currency $currency)
    {
        $currency->loadCount(['products', 'users', 'vendorProducts']);
        
        return view('currencies.show', compact('currency'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Currency $currency)
    {
        return view('currencies.edit', compact('currency'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Currency $currency)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:currencies,name,' . $currency->id,
            'symbol' => 'required|string|max:10',
            'position' => 'required|in:left,right',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $oldAttributes = $currency->toArray();
            $currency->update($validated);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($currency)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldAttributes,
                    'attributes' => $currency->toArray()
                ])
                ->log('Para birimi güncellendi');

            DB::commit();

            return redirect()
                ->route('admin.currencies.index')
                ->with('success', 'Para birimi başarıyla güncellendi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Para birimi güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Currency $currency)
    {
        // Varsayılan para birimi kontrolü
        if ($currency->id == 1) { // TL varsayılan olarak kabul ediliyor
            return redirect()
                ->back()
                ->with('error', 'Varsayılan para birimi silinemez.');
        }

        // İlişkili kayıtlar var mı kontrol et
        if ($currency->products()->exists() || 
            $currency->users()->exists() || 
            $currency->vendorProducts()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Kullanımda olan bir para birimi silinemez.');
        }

        DB::beginTransaction();
        try {
            // Log aktiviteyi kaydet
            activity()
                ->performedOn($currency)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $currency->toArray()])
                ->log('Para birimi silindi');

            $currency->delete();

            DB::commit();

            return redirect()
                ->route('admin.currencies.index')
                ->with('success', 'Para birimi başarıyla silindi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Para birimi silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }
}
