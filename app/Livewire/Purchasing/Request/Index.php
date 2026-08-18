<?php

namespace App\Livewire\Purchasing\Request;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\DisbursementType;
use Livewire\WithFileUploads;
class Index extends Component
{
    use WithFileUploads;
    public array $items = [];
    public function mount()
    {
        $this->addItem();
    }

    public function addItem(): void
    {
        $this->items[] = [
            'description' => '',
            'unit_price' => 0,
            'quantity' => 1,
            'remarks' => '',
            'image' => null,
        ];
    }

    public function removeItem(int $index): void
    {
        if (count($this->items) <= 1) {
            return;
        }

        unset($this->items[$index]);

        // 배열 index 정리
        $this->items = array_values($this->items);
    }

    public function updatedItems($value, $key): void
    {
        // 필요하면 여기서 실시간 validation
    }

    protected function rules(): array
    {
        return [
            'items.*.description' => [
                'required',
                'string',
                'max:255',
            ],

            'items.*.unit_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'items.*.remarks' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'items.*.image' => [
                'nullable',
                'image',
                'max:5120', // 5MB
            ],
        ];
    }

    public function save()
    {
        $this->validate();

        foreach ($this->items as $item) {

            $imagePath = null;

            if (!empty($item['image'])) {
                $imagePath = $item['image']->store(
                    'purchase-items',
                    'public'
                );
            }

            // 여기서 DB 저장
            //
            // PurchaseRequestItem::create([
            //     'description' => $item['description'],
            //     'unit_price' => $item['unit_price'],
            //     'quantity' => $item['quantity'],
            //     'remarks' => $item['remarks'],
            //     'image_path' => $imagePath,
            // ]);
        }

        session()->flash('success', 'Purchase request submitted successfully.');
    }
    
    #[Computed]
    public function disbursementTypes()
    {
        return DisbursementType::orderBy('name')->get();
    }
    public function render()
    {
        return view('livewire.purchasing.request.index');
    }
}
