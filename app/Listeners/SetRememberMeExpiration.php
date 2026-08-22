<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class SetRememberMeExpiration
{
    public function handle(Login $event): void
    {
        if ($event->remember) {
            $event->user->forceFill([
                'remember_expires_at' => now()->addDays(
                    config('auth.remember_days', 180)
                ),
            ])->save();

            return;
        }

        // 일반 로그인이라면 Remember Me 만료일 제거
        $event->user->forceFill([
            'remember_expires_at' => null,
        ])->save();
    }
}