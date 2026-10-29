<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    public function log(string $action, ?Model $auditable = null, array $meta = [], ?User $actor = null): AuditLog
    {
        $actorId = $actor ? $actor->id : Auth::id();

        // Sanitize meta: never store full card numbers, CVV, or passwords
        $safeMeta = $meta;
        unset($safeMeta['card_number'], $safeMeta['card_cvc'], $safeMeta['card_expiry'], $safeMeta['password']);

        return AuditLog::create([
            'actor_id' => $actorId,
            'action' => $action,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable ? $auditable->getKey() : null,
            'meta' => $safeMeta ?: null,
            'created_at' => now(),
        ]);
    }
}
