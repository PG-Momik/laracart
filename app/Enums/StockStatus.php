<?php

declare(strict_types=1);

namespace App\Enums;

enum StockStatus: string
{
    case IN_STOCK = 'in_stock';
    case LOW_STOCK = 'low_stock';
    case OUT_OF_STOCK = 'out_of_stock';

    public static function fromQuantity(int $quantity, int $threshold = 10): self
    {
        if ($quantity <= 0) {
            return self::OUT_OF_STOCK;
        }

        if ($quantity <= $threshold) {
            return self::LOW_STOCK;
        }

        return self::IN_STOCK;
    }

    public function label(): string
    {
        return match ($this) {
            self::IN_STOCK     => 'In Stock',
            self::LOW_STOCK    => 'Low Stock',
            self::OUT_OF_STOCK => 'Out of Stock',
        };
    }

    /**
     * Get the color class for UI display
     */
    public function color(): string
    {
        return match ($this) {
            self::IN_STOCK     => 'green',
            self::LOW_STOCK    => 'yellow',
            self::OUT_OF_STOCK => 'red',
        };
    }
}
