<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TransactionItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            // 'product'           => new ProductResource($this->whenLoaded('product')),
            'product'     => [
                'id'    => $this->product->id,
                'name'  => $this->product->name ?? null,
                'image' => $this->product && $this->product->image ? asset(Storage::url($this->product->image)) : null,
            ],
            'price'       => (float)(string) $this->price,
            'quantity'    => $this->quantity,
            'subtotal'    => (float)(string) $this->subtotal,
        ];
    }
}
