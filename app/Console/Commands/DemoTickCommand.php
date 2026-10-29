<?php

namespace App\Console\Commands;

use App\Services\BookingService;
use App\Services\PaymentService;
use Illuminate\Console\Command;

class DemoTickCommand extends Command
{
    protected $signature = 'demo:tick';
    protected $description = 'Execute all scheduled demo transition tasks once (expire, auto-complete, reminders)';

    public function handle(PaymentService $paymentService, BookingService $bookingService): int
    {
        $this->info('Running demo tick scheduled tasks...');

        $expired = $paymentService->expireAuthorizations();
        $this->line(" - Expired authorizations: {$expired}");

        $autoCompleted = $bookingService->autoCompleteDueBookings();
        $this->line(" - Auto-completed bookings: {$autoCompleted}");

        $reminders = $bookingService->sendReminders();
        $this->line(" - Reminders sent: {$reminders}");

        $this->info('Demo tick complete.');

        return self::SUCCESS;
    }
}
