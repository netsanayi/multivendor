<?php

namespace App\Modules\Users\Services;

use App\Models\User;
use App\Modules\Uploads\Services\UploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UserService
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Get filtered users
     */
    public function getFiltered($filters = [], $perPage = 20)
    {
        $query = User::with(['roles', 'defaultCurrency']);

        // Search
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Role filter
        if (isset($filters['role'])) {
            $query->role($filters['role']);
        }

        // Status filter
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Email verified filter
        if (isset($filters['email_verified'])) {
            if ($filters['email_verified']) {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        // Date range filter
        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Create a new user
     */
    public function create(array $data)
    {
        DB::beginTransaction();
        
        try {
            // Hash password
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            
            // Handle email verification
            if (isset($data['email_verified']) && $data['email_verified']) {
                $data['email_verified_at'] = now();
            }
            
            // Handle profile photo
            if (isset($data['profile_photo'])) {
                $upload = $this->uploadService->upload($data['profile_photo'], 'user_avatar', 'users/avatars');
                $data['profile_photo_path'] = $upload->file_path;
                unset($data['profile_photo']);
            }
            
            // Create user
            $user = User::create($data);
            
            // Assign role
            if (isset($data['role'])) {
                $user->assignRole($data['role']);
            }
            
            // Log activity
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $user->toArray()])
                ->log('Kullanıcı oluşturuldu');
            
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update a user
     */
    public function update(User $user, array $data)
    {
        DB::beginTransaction();
        
        try {
            $oldAttributes = $user->toArray();
            
            // Hash password if provided
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            
            // Handle email verification
            if (isset($data['email_verified'])) {
                if ($data['email_verified'] && !$user->email_verified_at) {
                    $data['email_verified_at'] = now();
                } elseif (!$data['email_verified']) {
                    $data['email_verified_at'] = null;
                }
                unset($data['email_verified']);
            }
            
            // Handle profile photo
            if (isset($data['profile_photo'])) {
                // Delete old photo
                if ($user->profile_photo_path) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }
                
                $upload = $this->uploadService->upload($data['profile_photo'], 'user_avatar', 'users/avatars');
                $data['profile_photo_path'] = $upload->file_path;
                unset($data['profile_photo']);
            }
            
            // Update user
            $user->update($data);
            
            // Update role
            if (isset($data['role'])) {
                $user->syncRoles([$data['role']]);
            }
            
            // Log activity
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldAttributes,
                    'attributes' => $user->toArray()
                ])
                ->log('Kullanıcı güncellendi');
            
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete a user
     */
    public function delete(User $user)
    {
        DB::beginTransaction();
        
        try {
            // Check if user is not deleting themselves
            if ($user->id === auth()->id()) {
                throw new \Exception('Kendi hesabınızı silemezsiniz.');
            }
            
            // Check if user is super admin
            if ($user->hasRole('super-admin')) {
                throw new \Exception('Super admin kullanıcısı silinemez.');
            }
            
            // Delete profile photo
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            
            // Log activity
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $user->toArray()])
                ->log('Kullanıcı silindi');
            
            // Delete user
            $user->delete();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(User $user)
    {
        // Check if user is not toggling themselves
        if ($user->id === auth()->id()) {
            throw new \Exception('Kendi hesabınızın durumunu değiştiremezsiniz.');
        }
        
        $user->toggleStatus();
        
        // Log activity
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties(['new_status' => $user->status])
            ->log('Kullanıcı durumu değiştirildi');
        
        return $user;
    }

    /**
     * Change user password
     */
    public function changePassword(User $user, $newPassword)
    {
        $user->password = Hash::make($newPassword);
        $user->save();
        
        // Log activity
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('Kullanıcı şifresi değiştirildi');
        
        return $user;
    }

    /**
     * Verify user email
     */
    public function verifyEmail(User $user)
    {
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
            $user->save();
            
            // Log activity
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->log('Kullanıcı e-postası doğrulandı');
        }
        
        return $user;
    }

    /**
     * Assign role to user
     */
    public function assignRole(User $user, $role)
    {
        $user->syncRoles([$role]);
        
        // Log activity
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties(['role' => $role])
            ->log('Kullanıcıya rol atandı');
        
        return $user;
    }

    /**
     * Get user statistics
     */
    public function getStatistics()
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('status', true)->count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'unverified_users' => User::whereNull('email_verified_at')->count(),
            'new_today' => User::whereDate('created_at', today())->count(),
            'new_this_week' => User::where('created_at', '>=', now()->startOfWeek())->count(),
            'new_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
            'by_role' => User::selectRaw('COUNT(*) as count')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->groupBy('roles.name')
                ->pluck('count', 'roles.name'),
        ];
    }

    /**
     * Get online users
     */
    public function getOnlineUsers()
    {
        return User::where('last_activity', '>=', now()->subMinutes(5))
            ->where('status', true)
            ->get();
    }

    /**
     * Search users
     */
    public function search($query)
    {
        return User::where('status', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();
    }

    /**
     * Export users to CSV
     */
    public function exportToCsv($users)
    {
        $csv = [];
        $csv[] = ['ID', 'Name', 'Email', 'Phone', 'Role', 'Status', 'Verified', 'Created At'];

        foreach ($users as $user) {
            $csv[] = [
                $user->id,
                $user->name,
                $user->email,
                $user->phone_number,
                $user->roles->first()->name ?? '-',
                $user->status ? 'Active' : 'Inactive',
                $user->email_verified_at ? 'Yes' : 'No',
                $user->created_at->format('Y-m-d H:i:s')
            ];
        }

        return $csv;
    }

    /**
     * Import users from CSV
     */
    public function importFromCsv($file)
    {
        $data = array_map('str_getcsv', file($file));
        $headers = array_shift($data);
        
        $imported = 0;
        $failed = 0;
        
        foreach ($data as $row) {
            try {
                $userData = array_combine($headers, $row);
                $this->create([
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'email' => $userData['email'],
                    'password' => $userData['password'] ?? 'password123',
                    'phone_number' => $userData['phone'] ?? null,
                    'role' => $userData['role'] ?? 'customer',
                    'status' => true,
                ]);
                $imported++;
            } catch (\Exception $e) {
                $failed++;
            }
        }
        
        return [
            'imported' => $imported,
            'failed' => $failed,
        ];
    }

    /**
     * Get users by role
     */
    public function getUsersByRole($role)
    {
        return User::role($role)
            ->where('status', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get recent registrations
     */
    public function getRecentRegistrations($days = 7)
    {
        return User::where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
