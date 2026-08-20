<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'code',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function parent()
    {
        return $this->belongsTo(
            Category::class,
            'parent_id'
        );
    }

    public function children()
    {
        return $this->hasMany(
            Category::class,
            'parent_id'
        );
    }

    public function childrenRecursive()
    {
        return $this->children()
            ->with('childrenRecursive');
    }

    public function descendants()
    {
        return $this->childrenRecursive();
    }
}