<?php

namespace App\Livewire\Procurements;

use App\Models\PurchaseAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use App\Services\PurchaseAmountService;

class Handover extends Component
{
    use WithPagination, WithFileUploads;

    public $selectedPurchaseActionId;
    public $handoverPhotoModal;
    public $handoverPhoto;
    
    public function openHandoverModal($purchaseActionId)
    {
        $this->selectedPurchaseActionId = $purchaseActionId;
        $this->handoverPhoto = null;
        $this->resetValidation();
        $this->handoverPhotoModal = true;
        $this->dispatch('reset-item-photo');
    }
    public function saveHandoverPhoto()
    {
        $this->validate([
            'handoverPhoto' => [
                'required',
                'image',
                'max:5120',
            ],
        ]);
        $purchaseAction = PurchaseAction::findOrFail($this->selectedPurchaseActionId);
        abort_unless($purchaseAction->action === 'purchased', 403);
        DB::transaction(function () use ($purchaseAction) {
            // Store receipt photo
            $path = Storage::disk('local')->putFile(
                'procurements/handover/' . now()->format('Y/m/d'),
                $this->handoverPhoto
            );
            $purchaseAction->itemHandoverPhotos()->create([
                'receiver_photo_path' => $path
            ]);
        });
        $this->reset([
            'selectedPurchaseActionId',
            'handoverPhotoModal',
            'handoverPhoto',
        ]);
        $this->dispatch('reset-item-photo');
    }
    public function render()
    {
        $purchase_actions = auth()->user()
            ->purchaseActions()
            ->where('action', 'purchased')
            ->whereHas('receivedItemPhotos')
            ->doesntHave('itemHandoverPhotos')
            ->with([
                'purchaseWorkflowItem.purchaseItem.item.primaryImage',
                'purchaseWorkflowItem.purchaseItem.purchaseRequest.user',
                'purchaseWorkflowItem.purchaseItem.purchaseRequest.department',
            ])
            ->latest()
            ->paginate(10);
        $purchase_actions->getCollection()->transform(function ($purchaseAction) {
            $purchaseItem = $purchaseAction->purchaseWorkflowItem?->purchaseItem;
            $quantity = (float) ($purchaseItem?->quantity ?? 0);
            $unitPrice = (float) ($purchaseItem?->unit_price ?? 0);
            $shippingFee = (float) ($purchaseItem?->shipping_fee ?? 0);
            $discount = (float) ($purchaseItem?->discount ?? 0);
            $itemAmount = $unitPrice * $quantity;
            $itemTotal = max(
                0,
                $itemAmount + $shippingFee - $discount
            );
            if ($purchaseItem->disbursement_type_name === 'Cash') {
                $amount = app(PurchaseAmountService::class)->calculate($purchaseItem);
                $itemTotal = app(PurchaseAmountService::class)->round($amount);
            }
            $purchaseAction->quantity = $quantity;
            $purchaseAction->unit_price_display = $unitPrice;
            $purchaseAction->item_amount = $itemAmount;
            $purchaseAction->shipping_fee_amount = $shippingFee;
            $purchaseAction->discount_amount = $discount;
            $purchaseAction->item_total = $itemTotal;
            $purchaseAction->vendor_name_display = $purchaseItem?->vendor_name;
            return $purchaseAction;
        });
        return view('livewire.procurements.handover', [
            'purchase_actions' => $purchase_actions,
        ]);
    }
}
