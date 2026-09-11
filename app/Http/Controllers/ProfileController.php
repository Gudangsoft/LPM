<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Support\UserAgentParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile / account settings page.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load(['roles', 'auditor', 'prodiDikepalai']);

        $sessions = collect();

        // Only meaningful when sessions are persisted (config('session.driver') === 'database');
        // on other drivers there is nothing to list, so just show the current one.
        if (config('session.driver') === 'database') {
            $sessions = DB::table(config('session.table', 'sessions'))
                ->where('user_id', $user->id)
                ->orderByDesc('last_activity')
                ->get()
                ->map(function ($session) use ($request) {
                    $agent = UserAgentParser::parse($session->user_agent);

                    return (object) [
                        'id' => $session->id,
                        'ip_address' => $session->ip_address,
                        'is_current' => $session->id === $request->session()->getId(),
                        'last_active' => \Illuminate\Support\Carbon::createFromTimestamp($session->last_activity),
                        'browser' => $agent['browser'],
                        'os' => $agent['os'],
                        'icon' => $agent['icon'],
                    ];
                });
        }

        return view('profile.edit', [
            'user' => $user,
            'sessions' => $sessions,
        ]);
    }

    /**
     * Update the user's profile information (name, email, avatar).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->safe()->only(['name', 'email']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->boolean('remove_avatar') && $user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->avatar = null;
        } elseif ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Sign this account out of every browser session except the current one.
     */
    public function logoutOtherSessions(Request $request): RedirectResponse
    {
        $request->validateWithBag('logoutOtherSessions', [
            'password' => ['required', 'current_password'],
        ]);

        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $request->user()->id)
                ->where('id', '!=', $request->session()->getId())
                ->delete();
        }

        return Redirect::route('profile.edit')->with('status', 'other-sessions-closed');
    }

    /**
     * Sign out one specific device/session (not the current one).
     */
    public function revokeSession(Request $request, string $session): RedirectResponse
    {
        abort_if($session === $request->session()->getId(), 422, 'Tidak dapat mengeluarkan sesi yang sedang Anda gunakan.');

        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $request->user()->id)
                ->where('id', $session)
                ->delete();
        }

        return Redirect::route('profile.edit')->with('status', 'session-revoked');
    }
}
