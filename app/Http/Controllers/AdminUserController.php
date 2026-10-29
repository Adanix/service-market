<?php

namespace App\Http\Controllers;

use App\Enums\ProviderStatus;
use App\Enums\UserStatus;
use App\Http\Requests\UpdateUserStatusRequest;
use App\Models\County;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function __construct(
        protected AuditService $audit
    ) {}

    public function index(Request $request): View
    {
        $counties = County::where('is_active', true)->orderBy('name')->get();
        $query = User::with(['county', 'providerProfile']);

        if ($request->filled('role')) {
            $query->where('role', $request->query('role'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('county')) {
            $query->where('county_id', $request->query('county'));
        }

        if ($request->filled('q')) {
            $search = $request->query('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest('created_at')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users', 'counties'));
    }

    public function show(User $user): View
    {
        $user->load(['county', 'providerProfile.services', 'bookings']);

        return view('admin.users.show', compact('user'));
    }

    public function updateStatus(UpdateUserStatusRequest $request, User $user): RedirectResponse
    {
        return $this->status($request, $user);
    }

    public function status(UpdateUserStatusRequest $request, User $user): RedirectResponse
    {
        // Prevent admin from suspending themselves
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot suspend the signed-in administrator.');
        }

        $newStatus = UserStatus::from($request->input('status'));

        $user->update(['status' => $newStatus]);

        // If user is a provider and suspended, also suspend provider profile
        if ($newStatus === UserStatus::Suspended && $user->providerProfile) {
            $user->providerProfile->update([
                'status' => ProviderStatus::Suspended,
                'suspension_reason' => 'User account suspended by administrator.',
            ]);
        }

        $this->audit->log('user.status_updated', $user, [
            'status' => $newStatus->value,
        ], auth()->user());

        return back()->with('success', "User status updated to {$newStatus->value}.");
    }
}
