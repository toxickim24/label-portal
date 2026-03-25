<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the client's profile
     */
    public function show(): Response
    {
        $user = auth()->user();

        // Try to find associated contact record with shared notes
        $contact = \App\Models\Contact::where('email', $user->email)
            ->with([
                'assignedUser',
                'tags',
                'notes' => function ($query) {
                    $query->visibleToClient()
                        ->with('user')
                        ->orderBy('is_pinned', 'desc')
                        ->orderBy('created_at', 'desc');
                }
            ])
            ->first();

        return Inertia::render('Client/Profile', [
            'contact' => $contact,
        ]);
    }

    /**
     * Update the client's profile information
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:50'],
            'zip' => ['nullable', 'string', 'max:20'],
        ]);

        $user = $request->user();

        // Track what changed
        $changes = [];
        if ($user->name !== $validated['name']) {
            $changes[] = 'name';
        }

        // Update user name
        $user->update(['name' => $validated['name']]);

        // Update associated contact record if exists
        $contact = \App\Models\Contact::where('email', $user->email)->first();
        if ($contact) {
            $contactChanges = [];

            if (isset($validated['phone']) && $contact->phone !== $validated['phone']) {
                $contactChanges['phone'] = $validated['phone'];
                $changes[] = 'phone';
            }
            if (isset($validated['address']) && $contact->address !== $validated['address']) {
                $contactChanges['address'] = $validated['address'];
                $changes[] = 'address';
            }
            if (isset($validated['city']) && $contact->city !== $validated['city']) {
                $contactChanges['city'] = $validated['city'];
                $changes[] = 'city';
            }
            if (isset($validated['state']) && $contact->state !== $validated['state']) {
                $contactChanges['state'] = $validated['state'];
                $changes[] = 'state';
            }
            if (isset($validated['zip']) && $contact->zip !== $validated['zip']) {
                $contactChanges['zip'] = $validated['zip'];
                $changes[] = 'zip';
            }

            if (!empty($contactChanges)) {
                $contact->update($contactChanges);
            }
        }

        // Log activity
        if (!empty($changes)) {
            app(\App\Services\ClientActivityService::class)->logProfileUpdate($user, $changes);
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the client's password
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Log activity
        app(\App\Services\ClientActivityService::class)->logPasswordChange($user, $request->ip());

        return back()->with('success', 'Password updated successfully.');
    }
}
