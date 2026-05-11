<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Gate;
use App\Models\User;

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
        Gate::define('superadmin', function (User $user) {
            return $user->role === 'superadmin';
        });

        // Dynamic Mail Configuration from Database
        try {
            if (class_exists(\App\Models\Setting::class)) {
                $settings = \App\Models\Setting::where('key', 'like', 'mail_%')->pluck('value', 'key');
                
                if ($settings->isNotEmpty()) {
                    config([
                        'mail.default' => $settings->get('mail_mailer', config('mail.default')),
                        'mail.mailers.smtp.host' => $settings->get('mail_host', config('mail.mailers.smtp.host')),
                        'mail.mailers.smtp.port' => $settings->get('mail_port', config('mail.mailers.smtp.port')),
                        'mail.mailers.smtp.encryption' => $settings->get('mail_encryption', config('mail.mailers.smtp.encryption')),
                        'mail.mailers.smtp.username' => $settings->get('mail_username', config('mail.mailers.smtp.username')),
                        'mail.mailers.smtp.password' => $settings->get('mail_password', config('mail.mailers.smtp.password')),
                        'mail.from.address' => $settings->get('mail_from_address', config('mail.from.address')),
                        'mail.from.name' => $settings->get('mail_from_name', config('mail.from.name')),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Avoid breaking during migrations or when DB is not ready
        }
    }
}
