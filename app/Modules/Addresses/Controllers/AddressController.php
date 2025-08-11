<?php

namespace App\Modules\Addresses\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Addresses\Models\Address;
use App\Modules\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource for a specific user.
     */
    public function index(Request $request)
    {
        $query = Address::with('user');
        
        // Search filter
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('address_name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%")
                  ->orWhere('street', 'like', "%{$search}%");
            });
        }
        
        // User filter
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        $addresses = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('addresses.index', compact('addresses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('addresses.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'address_name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'road_name' => 'nullable|string|max:255',
            'door_no' => 'required|string|max:50',
            'building_no' => 'nullable|string|max:50',
            'floor' => 'nullable|string|max:50',
            'company_type' => 'required|in:individual,corporate',
            'company_name' => 'required_if:company_type,corporate|nullable|string|max:255',
            'tax_office' => 'required_if:company_type,corporate|nullable|string|max:255',
            'tax_no' => 'required_if:company_type,corporate|nullable|string|max:50',
            'tc_id_no' => 'required_if:company_type,individual|nullable|string|size:11',
            'status' => 'required|boolean',
        ]);

        $validated['user_id'] = $request->user_id ?? auth()->id();

        DB::beginTransaction();
        try {
            $address = Address::create($validated);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($address)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $address->toArray()])
                ->log('Adres oluşturuldu');

            DB::commit();

            return redirect()
                ->route('admin.addresses.index')
                ->with('success', 'Adres başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Adres oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Address $address)
    {
        $address->load('user');
        return view('addresses.show', compact('address'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Address $address)
    {
        $users = User::orderBy('name')->get();
        return view('addresses.edit', compact('address', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Address $address)
    {
        // Kullanıcı ID güncelleme kontrolü
        if ($request->has('user_id') && auth()->user()->can('addresses.update')) {
            $validated['user_id'] = $request->user_id;
        }

        $validated = $request->validate([
            'address_name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'road_name' => 'nullable|string|max:255',
            'door_no' => 'required|string|max:50',
            'building_no' => 'nullable|string|max:50',
            'floor' => 'nullable|string|max:50',
            'company_type' => 'required|in:individual,corporate',
            'company_name' => 'required_if:company_type,corporate|nullable|string|max:255',
            'tax_office' => 'required_if:company_type,corporate|nullable|string|max:255',
            'tax_no' => 'required_if:company_type,corporate|nullable|string|max:50',
            'tc_id_no' => 'required_if:company_type,individual|nullable|string|size:11',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $oldAttributes = $address->toArray();
            $address->update($validated);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($address)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldAttributes,
                    'attributes' => $address->toArray()
                ])
                ->log('Adres güncellendi');

            DB::commit();

            return redirect()
                ->route('admin.addresses.index')
                ->with('success', 'Adres başarıyla güncellendi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Adres güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {

        DB::beginTransaction();
        try {
            // Log aktiviteyi kaydet
            activity()
                ->performedOn($address)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $address->toArray()])
                ->log('Adres silindi');

            $address->delete();

            DB::commit();

            return redirect()
                ->route('admin.addresses.index')
                ->with('success', 'Adres başarıyla silindi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Adres silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }
}
