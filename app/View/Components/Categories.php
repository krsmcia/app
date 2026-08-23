<?php

namespace App\View\Components;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Categories extends Component
{
    public $categories;

    public function __construct()
    {
        $this->categories = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->with('childrenRecursive')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function render(): View|Closure|string
    {
        return view('components.categories');
    }
}