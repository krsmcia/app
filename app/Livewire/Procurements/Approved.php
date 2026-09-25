<?php

namespace App\Livewire\Procurements;

use App\Models\PurchaseRequest;
use App\Models\PurchaseWorkflow;
use App\Models\PurchaseWorkflowItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Services\PurchaseWorkflowService;
use Livewire\Attributes\On;

class Approved extends Component
{
    use WithPagination, WithoutUrlPagination, WithFileUploads;
    public string $search = '';
    
    public ?int $selectedWorkflowItemId = null;
    public string $comment = '';
    public bool $commentModal = false;
    public bool $showAttachReceiptModal = false;
    public $recipientPhoto;
    public function openPlaceOrderModal(int $workflowItemId): void
    {
        $this->selectedWorkflowItemId = $workflowItemId;
        $this->comment = '';
        $this->commentModal = true;
    }
    public function purchaseItem()
    {
        $this->validate([
            'comment' => ['required', 'string', 'max:500'],
        ]);
        $workflowItem = PurchaseWorkflowItem::with('purchaseItem')
            ->findOrFail($this->selectedWorkflowItemId);
        abort_unless($workflowItem->status === 'pending', 403);
        DB::transaction(function () use ($workflowItem) {
            $workflowItem->update([
                'status' => 'ordered',
                'acted_at' => now(),
            ]);
            $workflowItem->purchaseActions()->create([
                'action' => 'ordered',
                'acted_by' => Auth::id(),
                'acted_at' => now(),
                'comment' => $this->comment,
            ]);
            app(PurchaseWorkflowService::class)->completeFundReleasedWorkflowIfFinished($workflowItem->purchaseWorkflow);
        });
        $this->reset([
            'commentModal',
            'selectedWorkflowItemId',
            'comment',
        ]);
    }
    //Upload receiver photo
    public function openAttachReceiptModal(int $workflowItemId): void
    {
        $this->selectedWorkflowItemId = $workflowItemId;
        $this->recipientPhoto = null;

        $this->resetValidation();

        $this->showAttachReceiptModal = true;

        $this->dispatch('reset-recipient-photo');
    }
    public function releaseCash(): void
    {
        $this->validate([
            'recipientPhoto' => [
                'required',
                'image',
                'max:5120',
            ],
            'comment' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);
        $workflowItem = PurchaseWorkflowItem::with([
            'purchaseItem.purchaseRequest',
        ])->findOrFail($this->selectedWorkflowItemId);
        abort_unless($workflowItem->status === 'pending', 403);
        DB::transaction(function () use ($workflowItem) {
            // Store receipt photo
            $path = Storage::disk('local')->putFile(
                'procurements/cash-releases/' . now()->format('Y/m/d'),
                $this->recipientPhoto
            );
            $workflowItem->update([
                'status' => 'purchased',
                'acted_at' => now(),
            ]);
            $purchaseAction = $workflowItem->purchaseActions()->create([
                'action' => 'purchased',
                'acted_by' => Auth::id(),
                'acted_at' => now(),
                'comment' => $this->comment,
            ]);
            $purchaseAction->cashRelease()->create([
                'receipt_photo_path' => $path
            ]);
            app(PurchaseWorkflowService::class)->completeFundReleasedWorkflowIfFinished($workflowItem->purchaseWorkflow);
        });
        $this->reset([
            'showAttachReceiptModal',
            'selectedWorkflowItemId',
            'recipientPhoto',
            'comment',
        ]);
        $this->dispatch('reset-recipient-photo');
    }
    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    private function roundCashAmount(float $amount): int
    {
        $whole = floor($amount);
        $decimal = $amount - $whole;

        return $decimal >= 0.45
            ? (int) $whole + 1
            : (int) $whole;
    }
    private function prepareWorkflowItem(
        PurchaseWorkflowItem $workflowItem
    ): PurchaseWorkflowItem {
        $purchaseItem = $workflowItem->purchaseItem;
        $unit_price = (float) ($purchaseItem->unit_price ?? 0);
        $quantity = (int) ($purchaseItem->quantity ?? 1);
        $amount = (float) ($unit_price * $quantity);
        $discount = (float) ($purchaseItem->discount ?? 0);
        $shippingFee = (float) ($purchaseItem->shipping_fee ?? 0);

        $amount;

        $calculatedTotal = max(
            0,
            $amount + $shippingFee - $discount
        );

        $disbursementType = $purchaseItem->itemVendor?->disbursementType;

        $isCash = strtolower(
            trim($disbursementType?->name ?? '')
        ) === 'cash';

        $releaseTotal = $isCash
            ? $this->roundCashAmount($calculatedTotal)
            : $calculatedTotal;

        /*
        |--------------------------------------------------------------------------
        | Latest Money Transaction
        |--------------------------------------------------------------------------
        */

        $latestMoneyTransaction = $purchaseItem->latestMoneyTransaction;
        $moneyHolder = match ($latestMoneyTransaction?->transaction?->type) {
            'released',
            'transfer' => $latestMoneyTransaction->transaction->toUser,
            'returned' => null,
            default => null,
        };

        /*
        |--------------------------------------------------------------------------
        | View Data
        |--------------------------------------------------------------------------
        */

        $workflowItem->purchase_item = $purchaseItem;

        $workflowItem->item = $purchaseItem->item;

        $workflowItem->item_vendor = $purchaseItem->itemVendor;

        $workflowItem->vendor = $purchaseItem->itemVendor?->vendor;

        $workflowItem->disbursement_type = $disbursementType;

        $workflowItem->payment_details =
            $purchaseItem->payment_details;

        $workflowItem->original_total = $amount;

        $workflowItem->calculated_total = $calculatedTotal;

        $workflowItem->release_total = $releaseTotal;

        $workflowItem->is_cash = $isCash;

        $workflowItem->money_holder = $moneyHolder;

        $workflowItem->latest_money_transaction = $latestMoneyTransaction;

        $workflowItem->is_money_holder = $moneyHolder?->id === auth()->id();

        /*
        |--------------------------------------------------------------------------
        | Display State
        |--------------------------------------------------------------------------
        */

        $workflowItem->status_label = match ($workflowItem->status) {
            'pending' => 'Pending',
            'ordered' => 'Ordered',
            'purchased' => 'Purchased',
            default => ucfirst($workflowItem->status),
        };

        $workflowItem->status_class = match ($workflowItem->status) {
            'pending'
                => 'bg-amber-100 text-amber-700',

            'ordered'
                => 'bg-blue-100 text-blue-700',

            'purchased'
                => 'bg-emerald-100 text-emerald-700',

            default
                => 'bg-gray-100 text-gray-700',
        };

        return $workflowItem;
    }
    private function prepareRequest(PurchaseRequest $request): PurchaseRequest
    {
        $workflow = $request->purchaseWorkflows->first();

        $request->audit_workflow = $workflow;

        if (!$workflow) {
            $request->items = collect();
            $request->audit_total = 0;
            $request->all_cash = false;

            return $request;
        }

        $request->items = $workflow->purchaseWorkflowItems->map(
            fn (PurchaseWorkflowItem $workflowItem) =>
                $this->prepareWorkflowItem($workflowItem)
        );

        $request->audit_total = max(
            0,
            $request->items->sum('release_total')
        );

        $request->all_cash = $request->items->isNotEmpty()
            && $request->items->every->is_cash;

        return $request;
    }
    #[On('cash-received')]
    public function refreshAfterCashReceived(): void
    {
    }
    public function render()
    {
        $requests = PurchaseRequest::query()
            ->with([
                'user',
                'department',

                'purchaseWorkflows' => function ($query) {
                    $query
                        ->where('step', 'fund released')
                        ->where('status', 'pending')
                        ->with([
                            'purchaseWorkflowItems' => function ($query) {
                                $query
                                    ->whereIn('status', ['pending', 'ordered'])
                                    ->with([
                                        'purchaseItem.item.primaryImage',
                                        'purchaseItem.itemVendor.vendor',
                                        'purchaseItem.itemVendor.disbursementType',
                                        'purchaseItem.latestMoneyTransaction.transaction.fromUser',
                                        'purchaseItem.latestMoneyTransaction.transaction.toUser',
                                    ]);
                            },
                        ]);
                },

                'purchaseRequestTransactions.transaction.fromUser',
                'purchaseRequestTransactions.transaction.toUser',
            ])
            ->whereHas('purchaseWorkflows', function ($query) {
                $query
                    ->where('step', 'fund released')
                    ->where('status', 'pending')
                    ->whereHas('purchaseWorkflowItems', function ($query) {
                        $query->whereIn('status', ['pending', 'ordered']);
                    });
            })
            ->latest()
            ->paginate(12);

        $requests->getCollection()->transform(
            fn (PurchaseRequest $request) => $this->prepareRequest($request)
        );

        return view('livewire.procurements.approved', [
            'requests' => $requests,
        ]);
    }
}