<?php

namespace Database\Seeders;

use App\Enums\ContactMessageStatus;
use App\Models\ContactMessage;
use Illuminate\Database\Seeder;

class ContactMessageSeeder extends Seeder
{
    public function run(): void
    {
        ContactMessage::updateOrCreate(
            ['email' => 'sarah.k@example.com', 'subject' => 'Question regarding coverage in West Cork'],
            [
                'name' => 'Sarah Kelly',
                'message' => 'Hello, I live just outside Clonakilty. Do providers listed under County Cork also service rural locations in West Cork?',
                'status' => ContactMessageStatus::New,
                'created_at' => now()->subHours(5),
            ]
        );

        ContactMessage::updateOrCreate(
            ['email' => 'paul.m@example.com', 'subject' => 'Feedback on demo checkout'],
            [
                'name' => 'Paul Murphy',
                'message' => 'Tested the simulated payment flow with demo cards. Everything worked smoothly without real credentials.',
                'status' => ContactMessageStatus::Read,
                'read_at' => now()->subDay(),
                'created_at' => now()->subDays(2),
            ]
        );
    }
}
