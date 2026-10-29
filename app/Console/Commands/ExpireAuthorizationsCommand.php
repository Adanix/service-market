<?php

namespace App\Console\Commands;

use App\Services\PaymentService;
use Illuminate\Console\Command;

class ExpireAuthorizationsCommand extends Command
{
    protected $signature = 'bookings:expire-authorizations';
    protected $description = 'Expire payment authorizations that have passed their capture window';

    public function handle(PaymentService $paymentService): int
    {
        $count = $paymentService->expireAuthorizations();
        $this->info("Expired authorizations: {$count}");

        return self::SUCCESS;
    }
}
