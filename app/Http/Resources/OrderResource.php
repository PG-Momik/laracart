<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'total_amount' => (float) $this->total_amount,
            'formatted_total' => '$' . number_format((float) $this->total_amount, 2),
            'status' => [
                'label' => $this->status->label(),
                'value' => $this->status->value,
                'color' => $this->status->color(),
            ],
            'items' => $this->whenLoaded('items', function () {
                return OrderItemResource::collection($this->items)->resolve();
            }),
            'items_preview' => $this->whenLoaded('items', function () {
                return $this->items->take(4)->map(fn($item) => [
                    'image_url' => $item->product->image_url,
                    'name' => $item->product->name
                ]);
            }),
            'item_count' => $this->whenLoaded('items', function () {
                return $this->items->sum('quantity');
            }),
            'created_at' => $this->created_at->toISOString(),
            'formatted_date' => $this->created_at->format('M d, Y H:i'),
        ];
    }
}
