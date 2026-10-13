<?php

namespace App\Livewire;

use Illuminate\Pagination\Cursor;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Attributes\Url;

class ActionHistory extends Component
{
    #[Url]
    public string $search = '';
    public int $perPage = 20;
    public array $purchaseActions = [];
    public ?string $nextCursor = null;
    public bool $hasMore = true;

    public function mount()
    {
        $this->loadPurchaseActions();
    }

    public function loadMore(): void
    {
        if (! $this->hasMore || ! $this->nextCursor) {
            return;
        }
        $this->loadPurchaseActions($this->nextCursor);
    }

    protected function loadPurchaseActions(?string $cursor = null): void
    {
        $query = auth()->user()->purchaseActions()
            ->with([
                'purchaseWorkflowItem.purchaseWorkflow',
                'purchaseWorkflowItem.purchaseItem.item.primaryImage',
            ])
            ->orderByDesc('purchase_actions.created_at')
            ->orderByDesc('purchase_actions.id');

        if ($cursor) {
            $cursor = Cursor::fromEncoded($cursor);
        }

        $results = $query->cursorPaginate(
            perPage: $this->perPage,
            columns: ['purchase_actions.*'],
            cursorName: 'cursor',
            cursor: $cursor,
        );

        foreach ($results->items() as $purchaseAction) {
            $this->purchaseActions[] = [
                // 필요한 데이터를 여기에 추가
                'id' => $purchaseAction->id,
                'image' => $purchaseAction->purchaseWorkflowItem->purchaseItem->item->primaryImage
                    ? Storage::url($purchaseAction->purchaseWorkflowItem->purchaseItem->item->primaryImage->path)
                    : asset('images/default-item.png'),
                'action' => $purchaseAction->action,
            ];
        }

        if ($results->nextCursor()) {
            $this->nextCursor = $results->nextCursor()->encode();
            $this->hasMore = true;
        } else {
            $this->nextCursor = null;
            $this->hasMore = false;
        }
    }

    public function render()
    {
        $total = auth()->user()->purchaseActions()->count();
        return view('livewire.action-history',[
            'purchaseActions' => $this->purchaseActions,
            'total' => $total
        ]);
    }
}
