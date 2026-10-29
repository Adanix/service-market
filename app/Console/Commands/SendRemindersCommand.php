<?php

namespace App\Console\Commands;

use App\Services\BookingService;
use Illuminate\Console\Command;

class SendRemindersCommand extends Command
{
    protected $signature = 'bookings:send-reminders';
    protected $description = 'Send reminder notifications for upcoming accepted bookings';

    public function handle(BookingService $bookingService): int
    {
        $count = $bookingService->sendReminders();
        $this->info("Reminder notifications sent: {$count}");

        return self::SUCCESS;
    }
}
