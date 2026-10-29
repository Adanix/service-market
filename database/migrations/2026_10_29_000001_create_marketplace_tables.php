<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('provider_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('business_name');
            $table->string('slug')->unique();
            $table->text('bio')->nullable();
            $table->foreignId('county_id')->constrained('counties')->restrictOnDelete();
            $table->string('avatar_path')->nullable();
            $table->string('status')->default('pending')->index();
            $table->timestamp('approved_at')->nullable();
            $table->text('suspension_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_profile_id')->constrained('provider_profiles')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('inclusions')->nullable();
            $table->text('exclusions')->nullable();
            $table->unsignedInteger('price_cents');
            $table->char('currency', 3)->default('EUR');
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->foreignId('county_id')->constrained('counties')->restrictOnDelete();
            $table->string('image_path')->nullable();
            $table->string('status')->default('draft');
            $table->text('moderation_note')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['county_id', 'status']);
            $table->index(['category_id', 'status']);
            $table->index(['status', 'published_at']);
        });

        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_profile_id')->constrained('provider_profiles')->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->date('period_start');
            $table->date('period_end');
            $table->unsignedInteger('amount_cents');
            $table->string('status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 16)->unique();
            $table->foreignId('customer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('provider_profile_id')->constrained('provider_profiles')->restrictOnDelete();
            $table->foreignId('service_id')->constrained('services')->restrictOnDelete();
            $table->string('status');

            // Snapshots
            $table->string('service_title');
            $table->string('category_name')->nullable();
            $table->string('provider_name');
            $table->string('county_name');
            $table->unsignedInteger('price_cents');
            $table->char('currency', 3)->default('EUR');
            $table->unsignedInteger('platform_fee_bps');
            $table->unsignedInteger('platform_fee_cents');
            $table->unsignedInteger('provider_net_cents');

            // Scheduling and details
            $table->dateTime('preferred_start_at')->index();
            $table->text('customer_note')->nullable();
            $table->text('decline_reason')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->string('cancelled_by')->nullable();

            // Timeline timestamps
            $table->dateTime('accepted_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('provider_completed_at')->nullable();
            $table->dateTime('customer_confirmed_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->dateTime('auto_complete_at')->nullable();
            $table->dateTime('authorization_expires_at')->nullable();
            $table->dateTime('reminder_sent_at')->nullable();

            $table->foreignId('payout_id')->nullable()->constrained('payouts')->nullOnDelete();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index(['provider_profile_id', 'status']);
            $table->index(['status', 'auto_complete_at']);
            $table->index(['status', 'authorization_expires_at']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained('bookings')->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->string('driver')->default('simulated');
            $table->string('status');
            $table->unsignedInteger('amount_cents');
            $table->char('currency', 3)->default('EUR');
            $table->string('card_brand')->nullable();
            $table->char('card_last4', 4)->nullable();
            $table->string('gateway_reference')->unique()->nullable();
            $table->string('idempotency_key')->unique();
            $table->unsignedTinyInteger('attempt_count')->default(1);
            $table->string('failure_code')->nullable();
            $table->string('failure_message')->nullable();
            $table->dateTime('authorized_at')->nullable();
            $table->dateTime('capture_before')->nullable();
            $table->dateTime('captured_at')->nullable();
            $table->dateTime('voided_at')->nullable();
            $table->dateTime('refunded_at')->nullable();
            $table->unsignedInteger('refund_amount_cents')->nullable();
            $table->timestamps();

            $table->index(['status', 'capture_before']);
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();

            $table->index(['auditable_type', 'auditable_id']);
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('subject');
            $table->text('message');
            $table->string('status')->default('new');
            $table->dateTime('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->string('notifiable_type');
            $table->unsignedBigInteger('notifiable_id');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('payouts');
        Schema::dropIfExists('services');
        Schema::dropIfExists('provider_profiles');
        Schema::dropIfExists('categories');
    }
};
