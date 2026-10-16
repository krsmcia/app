<?php

namespace App\Services;

use App\Models\PurchaseRequest;
use App\Notifications\WorkflowNotification;
use Illuminate\Support\Facades\Log;

class PurchaseRequestNotificationService
{
    public function notifyRequesterOfItemReceived(
        PurchaseRequest $purchaseRequest,
        ?string $comment = null
    ): void {
        $user = $purchaseRequest->user;

        if (! $user) {
            return;
        }

        $title = 'Purchase Item Received';

        $message = "Your purchase request {$purchaseRequest->request_no} has an item marked as received.";

        try {
            $user->notify(new WorkflowNotification(
                title: $title,
                message: $message,
                url: url('/purchase-requests'),
            ));
        } catch (\Throwable $e) {
            Log::error('Purchase item received notification failed.', [
                'purchase_request_id' => $purchaseRequest->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function notifyDenied(
        PurchaseRequest $purchaseRequest,
        ?string $comment = null
    ): void {
        $user = $purchaseRequest->user;

        if (! $user) {
            return;
        }

        $title = 'Purchase Request Denied';

        $message = "Your purchase request {$purchaseRequest->request_no} has been denied.";

        if ($comment) {
            $message .= " Reason: {$comment}";
        }

        try {
            $user->notify(new WorkflowNotification(
                title: $title,
                message: $message,
                url: url('/purchase-requests'),
            ));
        } catch (\Throwable $e) {
            Log::error('Purchase request denial notification failed.', [
                'purchase_request_id' => $purchaseRequest->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
    /**
     * usage
     * app(PurchaseRequestNotificationService::class)->notifyDenied($purchaseRequest,$comment);
     */
}