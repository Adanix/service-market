<?php

namespace App\Console\Commands;

use App\Services\BookingService;
use Illuminate\Console\Command;

class AutoCompleteBookingsCommand extends Command
{
    protected $signature = 'bookings:auto-complete';
    protected $description = 'Auto-complete bookings that have passed the customer confirmation window';

    public function handle(BookingService $bookingService): int
    {
        $count = $bookingService->autoCompleteDueBookings();
        $this->info("Auto-completed bookings: {$count}");

        return self::SUCCESS;
    }
}
