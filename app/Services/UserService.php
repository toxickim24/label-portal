<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Get paginated users with filters
     */
    public function getPaginatedUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::with(['roles', 'approvedBy', 'suspendedBy']);

        // Filter by status
        if (isset($filters['status'])) {
            switch ($filters['status']) {
                case 'pending':
                    $query->pendingApproval();
                    break;
                case 'approved':
                    $query->approved()->where('is_suspended', false);
                    break;
                case 'suspended':
                    $query->suspended();
                    break;
                case 'active':
                    $query->active();
                    break;
            }
        }

        // Filter by role
        if (isset($filters['role'])) {
            $query->role($filters['role']);
        }

        // Search
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Include trashed
        if (isset($filters['with_trashed']) && $filters['with_trashed']) {
            $query->withTrashed();
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new user
     */
    public function createUser(array $data): User
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_approved' => $data['is_approved'] ?? false,
            ]);

            if (isset($data['role'])) {
                $user->assignRole($data['role']);
            }

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update user
     */
    public function updateUser(User $user, array $data): User
    {
        DB::beginTransaction();
        try {
            $updateData = [
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
            ];

            if (isset($data['password']) && !empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            if (isset($data['role'])) {
                $user->syncRoles([$data['role']]);
            }

            DB::commit();
            return $user->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Approve user
     */
    public function approveUser(User $user, User $approver): User
    {
        $user->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $approver->id,
        ]);

        return $user->fresh();
    }

    /**
     * Suspend user
     */
    public function suspendUser(User $user, User $suspender): User
    {
        $user->update([
            'is_suspended' => true,
            'suspended_at' => now(),
            'suspended_by' => $suspender->id,
        ]);

        return $user->fresh();
    }

    /**
     * Unsuspend user
     */
    public function unsuspendUser(User $user): User
    {
        $user->update([
            'is_suspended' => false,
            'suspended_at' => null,
            'suspended_by' => null,
        ]);

        return $user->fresh();
    }

    /**
     * Delete user (soft delete)
     */
    public function deleteUser(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Restore user
     */
    public function restoreUser(int $userId): bool
    {
        $user = User::withTrashed()->findOrFail($userId);
        return $user->restore();
    }

    /**
     * Permanently delete user
     */
    public function forceDeleteUser(User $user): bool
    {
        return $user->forceDelete();
    }

    /**
     * Get user statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => User::count(),
            'active' => User::active()->count(),
            'pending' => User::pendingApproval()->count(),
            'suspended' => User::suspended()->count(),
            'admins' => User::role('admin')->count(),
            'agents' => User::role('agent')->count(),
            'clients' => User::role('client')->count(),
        ];
    }
}
