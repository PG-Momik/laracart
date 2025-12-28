<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'description'     => $this->description,
            'category'        => $this->category,
            'tags'            => $this->tags ?? [],
            'price'           => (float)$this->price,
            'formatted_price' => '$' . number_format((float)$this->price, 2),
            'stock_quantity'  => $this->stock_quantity,
            'total_stock'     => $this->total_stock,
            'stock_status'    => [
                'label' => $this->stock_status->label(),
                'value' => $this->stock_status->value,
                'color' => $this->stock_status->color(),
            ],
            'image_url'       => $this->image_url,
            'created_at'      => $this->created_at?->toIso8601String(),
            'updated_at'      => $this->updated_at?->toIso8601String(),
        ];
    }
}
