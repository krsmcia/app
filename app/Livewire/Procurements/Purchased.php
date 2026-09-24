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

class Purchased extends Component
{
    use WithPagination, WithoutUrlPagination, WithFileUploads;

    public $selectedPurchaseActionId;
    public $itemPhotoModal;
    public $itemPhoto;
    
    public function openReceiveModal($purchaseActionId)
    {
        $this->selectedPurchaseActionId = $purchaseActionId;
        $this->itemPhoto = null;
        $this->resetValidation();
        $this->itemPhotoModal = true;
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
        $purchaseAction = PurchaseAction::findOrFail($this->selectedPurchaseActionId);
        abort_unless($purchaseAction->action === 'purchased', 403);
        DB::transaction(function () use ($purchaseAction) {
            // Store receipt photo
            $path = Storage::disk('local')->putFile(
                'procurements/receive-item/' . now()->format('Y/m/d'),
                $this->itemPhoto
            );
            $purchaseAction->receivedItemPhotos()->create([
                'receipt_photo_path' => $path
            ]);
        });
        $this->reset([
            'selectedPurchaseActionId',
            'itemPhotoModal',
            'itemPhoto',
        ]);
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

        return view('livewire.procurements.purchased', [
            'purchase_actions' => $purchase_actions,
        ]);
    }
}
