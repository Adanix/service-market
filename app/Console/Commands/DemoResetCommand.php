<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DemoResetCommand extends Command
{
    protected $signature = 'demo:reset {--force : Force the operation to run without confirmation}';
    protected $description = 'Reset the demo database to fresh seeded state';

    public function handle(): int
    {
        if (!$this->option('force') && !$this->confirm('This will wipe all changes and reset demo data. Continue?')) {
            $this->comment('Operation cancelled.');
            return self::SUCCESS;
        }

        $this->info('Resetting database and running seeders...');

        $this->call('migrate:fresh', [
            '--seed' => true,
            '--force' => true,
        ]);

        $this->newLine();
        $this->info('Demo accounts restored:');

        $users = User::with(['county', 'providerProfile'])->get();
        $rows = [];
        foreach ($users as $user) {
            $rows[] = [
                $user->name,
                $user->email,
                $user->role->value,
                $user->county?->name ?? '-',
                $user->providerProfile?->business_name ?? '-',
                $user->status->value,
            ];
        }

        $this->table(['Name', 'Email', 'Role', 'County', 'Business', 'Status'], $rows);

        return self::SUCCESS;
    }
}
