<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class ProcurementRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $requestNo,
        public int $requestId,
    ) {}

    public function via($notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('New Purchase Request')
            ->body("Purchase request {$this->requestNo} requires review.")
            ->data([
                'url' => url('/procurements/requests'),
            ]);
    }
}