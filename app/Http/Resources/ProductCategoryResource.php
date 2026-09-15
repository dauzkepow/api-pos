<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            // jika image ada maka akan mengeluarkan asset Storage::url
            // jika image tidak ada muncull null
            // 'image'         => $this->image,
            'image'         => $this->image ? asset(Storage::url($this->image)) : null,
            'name'          => $this->name,
            'description'   => $this->description,
        ];
    }
}
