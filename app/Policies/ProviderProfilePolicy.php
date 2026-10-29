<?php

namespace App\Policies;

use App\Models\ProviderProfile;
use App\Models\User;

class ProviderProfilePolicy
{
    public function view(?User $user, ProviderProfile $providerProfile): bool
    {
        if ($user && ($user->isAdmin() || $user->id === $providerProfile->user_id)) {
            return true;
        }

        return $providerProfile->isApproved() && $providerProfile->user->isActive();
    }

    public function update(User $user, ProviderProfile $providerProfile): bool
    {
        return $user->isAdmin() || $user->id === $providerProfile->user_id;
    }
}
