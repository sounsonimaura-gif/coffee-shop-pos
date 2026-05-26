<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    use HasFactory;

    public function items(): HasMany
    {
        return $this->hasMany(RecipeItem::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function menuSize(): BelongsTo
    {
        return $this->belongsTo(MenuSize::class);
    }

    protected $fillable = [
        'menu_item_id',
        'menu_size_id',
        'name',
        'estimated_cost',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'menu_item_id' => 'integer',
        'menu_size_id' => 'integer',
        'estimated_cost' => 'decimal:4',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];
}
