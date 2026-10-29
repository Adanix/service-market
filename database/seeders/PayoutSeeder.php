<?php

namespace Database\Seeders;

use App\Enums\PayoutStatus;
use App\Models\Booking;
use App\Models\Payout;
use App\Models\ProviderProfile;
use Illuminate\Database\Seeder;

class PayoutSeeder extends Seeder
{
    public function run(): void
    {
        $silva = ProviderProfile::where('slug', 'silva-garden-care')->firstOrFail();
        $booking6 = Booking::where('reference', 'BK-DEMO000006')->firstOrFail();

        $payout = Payout::updateOrCreate(
            ['reference' => 'PO-4F2K1'],
            [
                'provider_profile_id' => $silva->id,
                'period_start' => now()->subDays(35)->toDateString(),
                'period_end' => now()->subDays(25)->toDateString(),
                'amount_cents' => 3825,
                'status' => PayoutStatus::Paid,
                'paid_at' => now()->subDays(20),
                'note' => 'Covering completed lawn maintenance service in Cork.',
            ]
        );

        $booking6->update(['payout_id' => $payout->id]);
    }
}
