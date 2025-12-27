<?php

declare(strict_types=1);

namespace App\Enums;

enum ProductSortBy: string
{
    case CREATED_AT = 'created_at';
    case NAME = 'name';
    case PRICE = 'price';
    case STOCK = 'stock_quantity';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
