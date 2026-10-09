<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use Illuminate\Notifications\Notification;

class ProcurementRequestNotification extends Notification
{
    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage) 
            ->title('Approved!') 
            ->body('Your account was approved!') 
            //->icon('/approved-icon.png') 
            //->badge('/badge.png') 
            ->action('View account', 'view_account') 
            ->data([ 'url' => url('/dashboard'), ]) 
            ->options([ 'TTL' => 3600, ]) 
            ->vibrate([300, 100, 300, 100, 500]);
            // ->badge()
            // ->dir()
            // ->image()
            // ->lang()
            // ->renotify()
            // ->requireInteraction()
            // ->tag()
    }
}
