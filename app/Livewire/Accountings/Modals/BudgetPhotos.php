<?php
namespace App\Livewire\Accountings\Modals;

use App\Models\Transaction;
use Livewire\Attributes\On;
use Livewire\Component;

class BudgetPhotos extends Component
{
    public bool $showModal = false;
    public array $photos = [];

    #[On('open-budget-photos')]
    public function open(int $transactionId): void
    {
        $transaction = Transaction::with('budgetReleasePhotos')
            ->findOrFail($transactionId);
        $this->photos = $transaction->budgetReleasePhotos
            ->map(fn ($photo) => [
                'id' => $photo->id,
                'path' => $photo->budget_receiver_photo_path,
            ])
            ->all();
        $this->showModal = true;
    }
    public function close(): void
    {
        $this->showModal = false;
        $this->photos = [];
    }
    public function render()
    {
        return view('livewire.accountings.modals.budget-photos');
    }
}