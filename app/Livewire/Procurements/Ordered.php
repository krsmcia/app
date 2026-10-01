<?php

namespace App\Livewire\Procurements;

use App\Models\PurchaseAction;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class Ordered extends Component
{
    use WithPagination, WithoutUrlPagination, WithFileUploads;

    public $selectedPurchaseActionId;
    public $itemPhotoModal = false;
    public $originalAmount = 0;
    public $amount = null;
    public string $comment = '';
    public $itemPhoto;

    public function openReceiveModal($purchaseActionId)
    {
        $purchaseAction = PurchaseAction::query()
            ->where('id', $purchaseActionId)
            ->where('action', 'ordered')
            ->with([
                'purchaseWorkflowItem.purchaseItem',
            ])
            ->firstOrFail();

        $purchaseItem = $purchaseAction
            ->purchaseWorkflowItem
            ?->purchaseItem;

        abort_unless($purchaseItem, 404);

        $this->selectedPurchaseActionId = $purchaseAction->id;

        $this->itemPhoto = null;

        $this->originalAmount = $this->calculateItemTotal(
            $purchaseItem
        );

        $this->amount = '';

        $this->comment = '';

        $this->resetValidation();

        $this->itemPhotoModal = true;
    }

    private function calculateItemTotal($purchaseItem): float
    {
        $amount =
            (float) $purchaseItem->quantity
            * (float) $purchaseItem->unit_price;

        $shippingFee =
            (float) ($purchaseItem->shipping_fee ?? 0);

        $discount =
            (float) ($purchaseItem->discount ?? 0);

        return max(
            0,
            $amount + $shippingFee - $discount
        );
    }

    public function saveItemPhoto()
    {
        /*
         * Remove comma formatting before validation.
         *
         * Example:
         * 1,234.50 -> 1234.50
         */
        if ($this->amount !== null) {
            $this->amount = str_replace(
                ',',
                '',
                (string) $this->amount
            );
        }

        $this->validate([
            'itemPhoto' => [
                'required',
                'image',
                'max:5120',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],
            'comment' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        $purchaseAction = PurchaseAction::query()
            ->where('id', $this->selectedPurchaseActionId)
            ->where('action', 'ordered')
            ->with([
                'purchaseWorkflowItem.purchaseItem.itemVendor',
            ])
            ->firstOrFail();

        $workflowItem = $purchaseAction->purchaseWorkflowItem;

        $purchaseItem = $workflowItem?->purchaseItem;

        abort_unless($workflowItem && $purchaseItem, 404);

        DB::transaction(function () use (
            $purchaseAction,
            $workflowItem,
            $purchaseItem
        ) {
            /*
             * Store received item photo.
             */
            $path = Storage::disk('local')->putFile(
                'procurements/received-item/' . now()->format('Y/m/d'),
                $this->itemPhoto
            );

            /*
             * Change workflow item:
             *
             * ordered
             *      ↓
             * purchased
             */
            $workflowItem->update([
                'status' => 'purchased',
                'acted_at' => now(),
            ]);

            /*
             * Create purchased action.
             */
            $purchasedAction = $workflowItem
                ->purchaseActions()
                ->create([
                    'action' => 'purchased',
                    'acted_by' => Auth::id(),
                    'acted_at' => now(),
                    'comment' => $this->comment,
                ]);

            /*
             * Create transaction.
             */
            $transaction = Transaction::create([
                'from_user_id' => Auth::id(),
                'vendor_id' => $purchaseItem->itemVendor?->vendor_id,
                'type' => 'purchased',
                'amount' => (float) $this->amount,
                'remark' => $this->comment,
                'created_by' => Auth::id(),
            ]);

            /*
             * Connect transaction to purchase item.
             */
            $transaction
                ->purchaseItemTransactions()
                ->create([
                    'purchase_item_id' => $workflowItem->purchase_item_id,
                    'amount' => (float) $this->amount,
                ]);

            /*
             * Save received item photo
             * against the PURCHASED action.
             */
            $purchasedAction
                ->receivedItemPhotos()
                ->create([
                    'item_photo_path' => $path,
                ]);
        });

        $this->reset([
            'selectedPurchaseActionId',
            'itemPhotoModal',
            'itemPhoto',
            'originalAmount',
            'amount',
            'comment',
        ]);

        $this->dispatch('reset-item-photo');
    }

    private function preparePurchaseActions($purchaseActions)
    {
        return $purchaseActions->through(function ($purchaseAction) {
            $purchaseItem = $purchaseAction
                ->purchaseWorkflowItem
                ?->purchaseItem;

            /*
             * Item
             */
            $purchaseAction->item =
                $purchaseItem?->item;

            /*
             * Purchase request
             */
            $purchaseAction->request =
                $purchaseItem?->purchaseRequest;

            /*
             * Quantity
             */
            $purchaseAction->quantity =
                (float) ($purchaseItem?->quantity ?? 0);

            /*
             * Unit price
             */
            $purchaseAction->unitPrice =
                (float) ($purchaseItem?->unit_price ?? 0);

            /*
             * Item amount
             */
            $purchaseAction->amount =
                $purchaseAction->quantity
                * $purchaseAction->unitPrice;

            /*
             * Shipping
             */
            $purchaseAction->shippingFee =
                (float) ($purchaseItem?->shipping_fee ?? 0);

            /*
             * Discount
             */
            $purchaseAction->discount =
                (float) ($purchaseItem?->discount ?? 0);

            /*
             * Final item total
             */
            $purchaseAction->itemTotal = max(
                0,
                $purchaseAction->amount
                    + $purchaseAction->shippingFee
                    - $purchaseAction->discount
            );

            /*
             * Vendor
             */
            $purchaseAction->vendorName =
                $purchaseItem?->itemVendor?->vendor?->name;

            return $purchaseAction;
        });
    }

    public function render()
    {
        $purchaseActions = auth()->user()
            ->purchaseActions()
            ->where('action', 'ordered')
            ->whereDoesntHave('purchaseWorkflowItem.purchaseActions', function ($query) {
                $query->where('action', 'purchased');
            })
            ->with([
                'purchaseWorkflowItem.purchaseItem.item.primaryImage',
                'purchaseWorkflowItem.purchaseItem.itemVendor.vendor',
                'purchaseWorkflowItem.purchaseItem.purchaseRequest.user',
                'purchaseWorkflowItem.purchaseItem.purchaseRequest.department',
            ])
            ->latest()
            ->paginate(10);

        $purchaseActions = $this->preparePurchaseActions(
            $purchaseActions
        );

        return view('livewire.procurements.ordered', [
            'purchase_actions' => $purchaseActions,
        ]);
    }
}