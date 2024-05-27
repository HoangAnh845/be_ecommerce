<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Dùng để đăng ký các policies
        // policies là các quy tắc để kiểm tra quyền của user
        'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Dùng để đăng ký các policies
        // policies là các quy tắc để kiểm tra quyền của user
        $this->registerPolicies();

        Passport::tokensExpireIn(now()->addDays(15)); // Tonken hết hạn sau 15 ngày của bảng oauth_access_tokens
        Passport::refreshTokensExpireIn(now()->addDays(30)); // Token refresh hết hạn sau 30 ngày của bảng oauth_refresh_tokens
        Passport::personalAccessTokensExpireIn(now()->addDays(10)); // addMinutes(10): Token cá nhân hết hạn sau 6 tháng của bảng oauth_personal_access_clients


        Passport::tokensCan([
            'admin' => 'All Permissions',
            'user' => 'One Permissions',
            // 'user-login' => 'User Login',
            // 'operator' => 'Submit Transactions, View Daily Information, Access Mobile Application',
            // 'client' => 'View Client',
        ]);

        // VerifyEmail::toMailUsing(function ($notifiable, $url) { 
        //     return (new MailMessage)
        //         ->subject('Verify Email Address')
        //         ->line('Click the button below to verify your email address.')
        //         ->action('Verify Email Address', $url); 
        // });
    }
}
