<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use App\Services\ClientInvitationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        protected UserService $userService,
        protected ClientInvitationService $invitationService
    ) {
        // Authorization is handled in routes via role middleware
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['status', 'role', 'search', 'with_trashed']);

        $users = $this->userService->getPaginatedUsers($filters, 15);
        $statistics = $this->userService->getStatistics();
        $roles = Role::all(['id', 'name']);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'statistics' => $statistics,
            'roles' => $roles,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new user
     */
    public function create(): Response
    {
        $roles = Role::all(['id', 'name']);

        return Inertia::render('Admin/Users/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'is_approved' => 'boolean',
        ]);

        $this->userService->createUser($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user
     */
    public function show(User $user): Response
    {
        $user->load(['roles', 'permissions', 'approvedBy', 'suspendedBy', 'activities']);

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the user
     */
    public function edit(User $user): Response
    {
        $user->load('roles');
        $roles = Role::all(['id', 'name']);

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $this->userService->updateUser($user, $validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->userService->deleteUser($user);

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Approve a user
     */
    public function approve(User $user): RedirectResponse
    {
        $this->authorize('approve', $user);

        $this->userService->approveUser($user, auth()->user());

        return back()->with('success', 'User approved successfully.');
    }

    /**
     * Suspend a user
     */
    public function suspend(User $user): RedirectResponse
    {
        $this->authorize('suspend', $user);

        $this->userService->suspendUser($user, auth()->user());

        return back()->with('success', 'User suspended successfully.');
    }

    /**
     * Unsuspend a user
     */
    public function unsuspend(User $user): RedirectResponse
    {
        $this->authorize('suspend', $user);

        $this->userService->unsuspendUser($user);

        return back()->with('success', 'User unsuspended successfully.');
    }

    /**
     * Restore a deleted user
     */
    public function restore(int $id): RedirectResponse
    {
        $this->userService->restoreUser($id);

        return back()->with('success', 'User restored successfully.');
    }

    /**
     * Show the client invitation form
     */
    public function inviteClient(): Response
    {
        return Inertia::render('Admin/Users/InviteClient');
    }

    /**
     * Send client invitation
     */
    public function sendInvitation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        try {
            $this->invitationService->createInvitation($validated);

            return redirect()->route('admin.users.index')
                ->with('success', 'Client invitation sent successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Resend client invitation
     */
    public function resendInvitation(User $user): RedirectResponse
    {
        try {
            $this->invitationService->resendInvitation($user);

            return back()->with('success', 'Invitation resent successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
