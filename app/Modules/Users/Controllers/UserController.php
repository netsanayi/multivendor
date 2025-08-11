<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Roles\Models\Role;
use App\Modules\Currencies\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'defaultCurrency']);

        // Arama
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Rol filtresi
        if ($request->has('role_id')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('roles.id', $request->get('role_id'));
            });
        }

        // Durum filtresi
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Sıralama
        $query->orderBy('created_at', 'desc');

        $users = $query->paginate(20);
        $roles = Role::orderBy('name')->get();

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Geçici çözüm: is_active kolonu yoksa tüm rolleri getir
        try {
            $roles = Role::where('is_active', true)->orderBy('name')->get();
        } catch (\Exception $e) {
            $roles = Role::orderBy('name')->get();
        }
        
        try {
            $currencies = Currency::where('is_active', true)->orderBy('name')->get();
        } catch (\Exception $e) {
            $currencies = Currency::orderBy('name')->get();
        }

        return view('users.create', compact('roles', 'currencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|string|min:8|confirmed',
            'default_currency_id' => 'nullable|exists:currencies,id',
            'status' => 'required|boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];

        DB::beginTransaction();
        try {
            $user = User::create($validated);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $user->toArray()])
                ->log('Kullanıcı oluşturuldu');

            DB::commit();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Kullanıcı başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Kullanıcı oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['roles', 'defaultCurrency', 'addresses', 'vendorProducts']);
        
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Geçici çözüm: is_active kolonu yoksa tüm rolleri getir
        try {
            $roles = Role::where('is_active', true)->orderBy('name')->get();
        } catch (\Exception $e) {
            $roles = Role::orderBy('name')->get();
        }
        
        try {
            $currencies = Currency::where('is_active', true)->orderBy('name')->get();
        } catch (\Exception $e) {
            $currencies = Currency::orderBy('name')->get();
        }

        return view('users.edit', compact('user', 'roles', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:8|confirmed',
            'default_currency_id' => 'nullable|exists:currencies,id',
            'status' => 'required|boolean',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];

        DB::beginTransaction();
        try {
            $oldAttributes = $user->toArray();
            $user->update($validated);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldAttributes,
                    'attributes' => $user->toArray()
                ])
                ->log('Kullanıcı güncellendi');

            DB::commit();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Kullanıcı başarıyla güncellendi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Kullanıcı güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Kendi hesabını silmeye çalışıyorsa engelle
        if ($user->id === auth()->id()) {
            return redirect()
                ->back()
                ->with('error', 'Kendi hesabınızı silemezsiniz.');
        }

        // İlişkili verileri kontrol et
        if ($user->vendorProducts()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Ürünleri olan bir kullanıcı silinemez.');
        }

        DB::beginTransaction();
        try {
            // Log aktiviteyi kaydet
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $user->toArray()])
                ->log('Kullanıcı silindi');

            $user->delete();

            DB::commit();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Kullanıcı başarıyla silindi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Kullanıcı silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }
}
