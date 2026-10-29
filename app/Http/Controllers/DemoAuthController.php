<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DemoAuthController extends Controller
{
    public function __construct(
        protected AuditService $audit
    ) {}

    public function show(): View
    {
        $users = User::with(['county', 'providerProfile.county'])->get();

        $customers = $users->where('role', UserRole::Customer);
        $providers = $users->where('role', UserRole::Provider);
        $admins = $users->where('role', UserRole::Admin);

        return view('demo.sign-in', compact('customers', 'providers', 'admins'));
    }

    public function signIn(Request $request, User $user): RedirectResponse
    {
        if ($user->status !== UserStatus::Active) {
            return redirect()->route('demo.sign-in')
                ->with('error', 'This demo account is not active.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        $this->audit->log('auth.demo_login', $user, [
            'email' => $user->email,
            'role' => $user->role->value,
        ], $user);

        return match ($user->role) {
            UserRole::Admin => redirect()->route('admin.dashboard'),
            UserRole::Provider => redirect()->route('provider.dashboard'),
            default => redirect()->route('customer.dashboard'),
        };
    }

    public function signOut(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been signed out.');
    }
}
