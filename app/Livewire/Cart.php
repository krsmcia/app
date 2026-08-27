<?php

namespace App\Livewire;

use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class Cart extends Component
{
    public function createRequest(array $cartItems, string $remark = '')
    {
        $remark = trim($remark);
        if ($remark === '') {
            throw new \RuntimeException(
                'Remark is required.'
            );
        }
        if (empty($cartItems)) {
            $this->dispatch('cart-error', message: 'Your cart is empty.');
            return;
        }
        try {
            DB::transaction(function () use ($cartItems, $remark) {
                $user = auth()->user();
                $purchaseRequest = PurchaseRequest::create([
                    'request_no' => $this->generateRequestNo(),
                    'user_id' => $user->id,
                    'department_id' => $user->current_department_id,
                    'remark' => $remark,
                    'total_amount' => 0,
                ]);
                $totalAmount = 0;
                foreach ($cartItems as $cartItem) {
                    $purchaseRequest->items()->create([
                        'item_id' => $cartItem['id'],
                        'quantity' => max(
                            1,
                            min((int) ($cartItem['quantity'] ?? 1), 999)
                        ),
                        'item_name' => $cartItem['name'],
                        'sku' => $cartItem['sku'] ?? '',
                        'item_vendor_id' => null,
                        'vendor_name' => null,
                        'vendor_sku' => null,
                        'unit_price' => null,
                        'amount' => null,
                    ]);
                }
                $purchaseRequest->update([
                    'total_amount' => $totalAmount,
                ]);
                $nextStep = $this->nextApprovalStep($user);
                if (!$nextStep) {
                    throw new \RuntimeException(
                        'No approval workflow is available for this user.'
                    );
                }
                $workflow = $purchaseRequest->purchaseWorkflow()->create([
                    'step' => $nextStep,
                    'status' => 'pending',
                ]);
                foreach ($purchaseRequest->items as $purchaseItem) {
                    $workflow->purchaseWorkflowItems()->create([
                        'purchase_item_id' => $purchaseItem->id,
                        'status' => 'pending',
                    ]);
                }
            });
        } catch (\RuntimeException $e) {
            $this->dispatch(
                'cart-error',
                message: $e->getMessage()
            );

            return;
        }
        $this->dispatch('cart-request-created');
    }
    protected function nextApprovalStep($user): ?string
    {
        return match (true) {
            $user->hasRole('staff') => 'team-leader',
            $user->hasRole('team-leader') => 'supervisor',
            $user->hasRole('supervisor') => 'procurement',
            default => null,
        };
    }
    protected function generateRequestNo(): string
    {
        do {
            $requestNo = 'PR-' . now()->format('Ymd') . '-' . strtoupper(
                Str::random(6)
            );
        } while (
            PurchaseRequest::where('request_no', $requestNo)->exists()
        );
        return $requestNo;
    }
    public function render()
    {
        return view('livewire.cart');
    }
}