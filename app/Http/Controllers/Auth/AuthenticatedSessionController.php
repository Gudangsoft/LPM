<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $captchaNum1 = random_int(1, 10);
        $captchaNum2 = random_int(1, 10);

        session(['captcha_answer' => $captchaNum1 + $captchaNum2]);

        return view('auth.login', [
            'captchaNum1' => $captchaNum1,
            'captchaNum2' => $captchaNum2,
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Admin, and any other role granted access to the admin area
        // (auditor/kaprodi/viewer all carry dashboard.view), land on the dashboard.
        if (Auth::user()->isAdmin() || Auth::user()->hasAnyPermission(['dashboard.view'])) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('home'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}