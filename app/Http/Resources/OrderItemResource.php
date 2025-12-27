<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'quantity' => $this->quantity,
            'price_at_purchase' => (float) $this->price_at_purchase,
            'formatted_price' => '$' . number_format((float) $this->price_at_purchase, 2),
            'subtotal' => (float) $this->subtotal,
            'formatted_subtotal' => '$' . number_format((float) $this->subtotal, 2),
        ];
    }
}
