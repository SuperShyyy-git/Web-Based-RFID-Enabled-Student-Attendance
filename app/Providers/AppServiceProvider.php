<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use App\Models\Account;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production to prevent mixed content issues
        if (app()->environment('production')) {
            URL::forceScheme('https');
            
            // Auto-seed admin user if it doesn't exist
            $this->seedAdminUser();
        }
    }
    
    /**
     * Auto-seed the default admin user if not exists.
     * This runs once per deployment when the admin user is missing.
     */
    private function seedAdminUser(): void
    {
        try {
            // Only proceed if the accounts table exists
            if (!Schema::hasTable('accounts')) {
                return;
            }
            
            // Check if admin already exists
            $adminExists = Account::where('email', 'example@gmail.com')->exists();
            
            if (!$adminExists) {
                Account::create([
                    'email' => 'example@gmail.com',
                    'password' => Hash::make('159753'),
                    'role' => 'admin',
                ]);
                
                \Log::info('Admin user auto-seeded successfully.');
            }
        } catch (\Exception $e) {
            // Silently fail - this might run before migrations
            \Log::warning('Auto-seed admin failed: ' . $e->getMessage());
        }
    }
}
