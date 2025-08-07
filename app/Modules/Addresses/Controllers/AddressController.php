<?php

namespace App\Modules\Addresses\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Addresses\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource for a specific user.
     */
    public function index(User $user)
    {
        $addresses = $user->addresses()->orderBy('created_at', 'desc')->get();
        
        return view('addresses.index', compact('user', 'addresses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(User $user)
    {
        return view('addresses.create', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, User $user)
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

        $validated['user_id'] = $user->id;

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
                ->route('admin.users.addresses.index', $user)
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
    public function show(User $user, Address $address)
    {
        // Adresin kullanıcıya ait olduğunu kontrol et
        if ($address->user_id !== $user->id) {
            abort(404);
        }

        return view('addresses.show', compact('user', 'address'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user, Address $address)
    {
        // Adresin kullanıcıya ait olduğunu kontrol et
        if ($address->user_id !== $user->id) {
            abort(404);
        }

        return view('addresses.edit', compact('user', 'address'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user, Address $address)
    {
        // Adresin kullanıcıya ait olduğunu kontrol et
        if ($address->user_id !== $user->id) {
            abort(404);
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
                ->route('admin.users.addresses.index', $user)
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
    public function destroy(User $user, Address $address)
    {
        // Adresin kullanıcıya ait olduğunu kontrol et
        if ($address->user_id !== $user->id) {
            abort(404);
        }

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
                ->route('admin.users.addresses.index', $user)
                ->with('success', 'Adres başarıyla silindi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Adres silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }
}
