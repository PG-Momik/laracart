<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'product'              => $this->product ? (new ProductResource($this->product))->resolve() : null,
            'quantity'             => $this->quantity,
            'subtotal'             => (float)$this->subtotal,
            'formatted_subtotal'   => '$' . number_format((float)$this->subtotal, 2),
            'has_sufficient_stock' => $this->hasStockAvailable(),
        ];
    }
}
