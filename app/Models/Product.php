<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// mass asigment field mana saja yang diisi
#[Fillable(['product_category_id', 'image', 'name', 'price', 'stock'])]
class Product extends Model
{
    // cast
    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    // relasi ke product category
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    // melakukan query ketika ada search yang diisi
    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%");
        });
    }

    // Scope by Category
    public function scopeByCategory($query, $categoryId)
    {
        return $query->when($categoryId, function ($query, $categoryId) {
            $query->where('product_category_id', $categoryId);
        });
    }
}
