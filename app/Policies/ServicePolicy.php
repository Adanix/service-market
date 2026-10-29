<?php

namespace App\Policies;

use App\Enums\ProviderStatus;
use App\Enums\ServiceStatus;
use App\Enums\UserStatus;
use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Service $service): bool
    {
        // Owner or admin can view any of their services
        if ($user) {
            if ($user->isAdmin()) {
                return true;
            }
            if ($user->providerProfile && $user->providerProfile->id === $service->provider_profile_id) {
                return true;
            }
        }

        // Public visibility requires active service, approved provider, and active user
        return $service->status === ServiceStatus::Active
            && $service->providerProfile->status === ProviderStatus::Approved
            && $service->providerProfile->user->status === UserStatus::Active;
    }

    public function create(User $user): bool
    {
        return $user->isProvider() && $user->providerProfile !== null;
    }

    public function update(User $user, Service $service): bool
    {
        return $user->isAdmin()
            || ($user->isProvider() && $user->providerProfile && $user->providerProfile->id === $service->provider_profile_id);
    }

    public function submit(User $user, Service $service): bool
    {
        if (!$this->update($user, $service)) {
            return false;
        }

        // Provider must be approved to submit services
        return $user->providerProfile && $user->providerProfile->isApproved();
    }

    public function archive(User $user, Service $service): bool
    {
        return $this->update($user, $service);
    }

    public function moderate(User $user): bool
    {
        return $user->isAdmin();
    }
}
