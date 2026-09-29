<?php

namespace App\Livewire\Procurements;

use App\Models\PurchaseAction;
use App\Services\PurchaseAmountService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class Purchased extends Component
{
    use WithPagination, WithoutUrlPagination, WithFileUploads;

    public $selectedPurchaseActionId;
    public $itemPhotoModal = false;
    public $itemPhoto;

    public function openReceiveModal($purchaseActionId)
    {
        $this->selectedPurchaseActionId = $purchaseActionId;
        $this->itemPhoto = null;

        $this->resetValidation();

        $this->itemPhotoModal = true;

        $this->dispatch('reset-item-photo');
    }

    public function saveItemPhoto()
    {
        $this->validate([
            'itemPhoto' => [
                'required',
                'image',
                'max:5120',
            ],
        ]);

        $purchaseAction = PurchaseAction::findOrFail(
            $this->selectedPurchaseActionId
        );

        abort_unless(
            $purchaseAction->action === 'purchased',
            403
        );

        DB::transaction(function () use ($purchaseAction) {
            $path = Storage::disk('local')->putFile(
                'procurements/received-item/' . now()->format('Y/m/d'),
                $this->itemPhoto
            );
            $purchaseAction->receivedItemPhotos()->create([
                'receipt_photo_path' => $path,
            ]);
        });

        $this->reset([
            'selectedPurchaseActionId',
            'itemPhotoModal',
            'itemPhoto',
        ]);

        $this->dispatch('reset-item-photo');
    }

    public function render()
    {
        $purchase_actions = auth()->user()
            ->purchaseActions()
            ->where('action', 'purchased')
            ->doesntHave('receivedItemPhotos')
            ->with([
                'purchaseWorkflowItem.purchaseItem.item.primaryImage',
                'purchaseWorkflowItem.purchaseItem.purchaseRequest.user',
                'purchaseWorkflowItem.purchaseItem.purchaseRequest.department',
            ])
            ->latest()
            ->paginate(10);

        $amountService = app(PurchaseAmountService::class);

        $purchase_actions->getCollection()->transform(
            function ($purchaseAction) use ($amountService) {
                $purchaseItem = $purchaseAction
                    ->purchaseWorkflowItem
                    ?->purchaseItem;

                $quantity = (float) ($purchaseItem?->quantity ?? 0);
                $unitPrice = (float) ($purchaseItem?->unit_price ?? 0);
                $shippingFee = (float) ($purchaseItem?->shipping_fee ?? 0);
                $discount = (float) ($purchaseItem?->discount ?? 0);

                $itemAmount = $unitPrice * $quantity;

                $itemTotal = max(
                    0,
                    $itemAmount + $shippingFee - $discount
                );

                /*
                 * Cash purchases use the actual cash amount
                 * calculated and rounded by PurchaseAmountService.
                 */
                if (
                    strtolower(
                        (string) ($purchaseItem?->disbursement_type_name ?? '')
                    ) === 'cash'
                ) {
                    $amount = $amountService->calculate($purchaseItem);

                    $itemTotal = $amountService->round($amount);
                }

                $purchaseAction->quantity = $quantity;
                $purchaseAction->unit_price_display = $unitPrice;
                $purchaseAction->item_amount = $itemAmount;
                $purchaseAction->shipping_fee_amount = $shippingFee;
                $purchaseAction->discount_amount = $discount;
                $purchaseAction->item_total = $itemTotal;
                $purchaseAction->vendor_name_display = $purchaseItem?->vendor_name;

                return $purchaseAction;
            }
        );

        return view('livewire.procurements.purchased', [
            'purchase_actions' => $purchase_actions,
        ]);
    }
}