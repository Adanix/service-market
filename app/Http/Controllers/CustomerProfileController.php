<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCustomerProfileRequest;
use App\Models\County;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerProfileController extends Controller
{
    public function edit(): View
    {
        $customer = auth()->user();
        $counties = County::where('is_active', true)->orderBy('name')->get();

        return view('customer.profile.edit', compact('customer', 'counties'));
    }

    public function update(UpdateCustomerProfileRequest $request): RedirectResponse
    {
        $customer = $request->user();
        $customer->update($request->validated());

        return redirect()->route('customer.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
