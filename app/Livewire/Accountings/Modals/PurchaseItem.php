<?php

namespace App\Livewire\Accountings\Modals;

use Livewire\Component;
use App\Models\User;
use App\Models\PurchaseWorkflowItem;
use App\Models\Transaction;
use App\Services\PurchaseWorkflowService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Illuminate\Validation\ValidationException;
class PurchaseItem extends Component
{
    use WithPagination, WithoutUrlPagination;

    public bool $commentModal = false;
    public string $search = '';
    public string $comment = '';
    public ?int $releaseWorkflowItemId = null;
    public ?int $recipientUserId = null;

    #[On('purchase-modal')]
    public function openPurchaseModal($workflowItemId)
    {
        $this->releaseWorkflowItemId = $workflowItemId;
        $this->comment = '';
        $this->resetValidation();
        $this->commentModal = true;
    }
    public function complete()
    {
        $this->validate([
            'comment' => 'required|string|max:500',
            'recipientUserId' => 'required|integer|exists:users,id',
        ]);
        $recipient = User::whereKey($this->recipientUserId)
            ->whereHas('departments', function ($query) {
                $query->where('code', 'procurement')
                    ->where('is_active', true);
            })
            ->first();
            
        if (! $recipient) {
            throw ValidationException::withMessages([
                'recipientUserId' => 'The selected recipient must belong to the Procurement department.',
            ]);
        }
        
        if (!$this->releaseWorkflowItemId) {
            return;
        }
        $workflowItem = PurchaseWorkflowItem::query()
            ->with([
                'purchaseWorkflow',
                'purchaseItem',
            ])
            ->whereKey($this->releaseWorkflowItemId)
            ->where('status', 'pending')
            ->whereHas('purchaseWorkflow', function ($query) {
                $query
                    ->where('step', 'accounting')
                    ->where('status', 'pending');
            })
            ->firstOrFail();
        DB::transaction(function () use ($workflowItem, $recipient) {
            $purchaseItem = $workflowItem->purchaseItem;
            $isCash = strcasecmp(
                trim($purchaseItem->disbursement_type_name ?? ''),
                'cash'
            ) === 0;
            $workflowItem->update([
                'status' => $isCash ? 'fund released' : 'purchased',
                'acted_at' => now(),
            ]);
            // Amount transfered
            $workflowItem->purchaseActions()->create([
                'action' => 'transfer',
                'acted_by' => auth()->id(),
                'acted_at' => now(),
                'comment' => $this->comment,
            ]);
            // charge of the item
            $workflowItem->purchaseActions()->create([
                'action' => 'purchased',
                'acted_by' => $recipient->id,
                'acted_at' => now(),
                'comment' => $this->comment,
            ]);
            Transaction::create([
                'from_user_id' => auth()->id(), //Accounting user who log in now
                'to_user_id' => $recipient->id,
                'vendor_id' => $purchaseItem->itemVendor->vendor_id,
                'type' => 'purchased',
                'amount' => $this->calculate($purchaseItem),
                'remark' => $this->comment,
                'created_by' => auth()->id()
            ]);
            app(PurchaseWorkflowService::class)->completeAccountingWorkflowIfFinished($workflowItem->purchaseWorkflow);
        });
        $this->reset([
            'commentModal',
            'comment',
            'releaseWorkflowItemId',
        ]);
        $this->dispatch('approval-updated');
        $this->dispatch('cash-released')->to(\App\Livewire\Accountings\Requests::class);
    }
    private function calculate($purchaseItem): float
    {
        $amount = (float) $purchaseItem->unit_price * (int) $purchaseItem->quantity;
        $discount = (float) ($purchaseItem->discount ?? 0);
        $shippingFee = (float) ($purchaseItem->shipping_fee ?? 0);

        return $amount - $discount + $shippingFee;
    }
    public function updatedSearch(): void
    {
        $this->recipientUserId = null;
        $this->resetPage();
    }
    public function render()
    {
        $users = User::query()
            ->whereHas('departments', function ($query) {
                $query->where('code', 'procurement');
            })
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query
                        ->where(
                            'name',
                            'like',
                            '%' . $this->search . '%'
                        )
                        ->orWhere(
                            'email',
                            'like',
                            '%' . $this->search . '%'
                        );
                });
            })
            ->orderBy('name')
            ->paginate(10);
        return view('livewire.accountings.modals.purchase-item', ['users' => $users,]);
    }
}
