<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

// mass asigment field mana saja yang diisi
#[Fillable(['image', 'name', 'description'])]
class ProductCategory extends Model
{
    // melakukan query ketika ada search yang diisi
    public function scopeSearch($query, $search)
    {
        return $query->when($$search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%");
        });
    }
}
