<?php

declare(strict_types=1);

namespace App\Enums;

enum StockAlertType: string
{
    case OUT_OF_STOCK = 'OUT OF STOCK';
    case LOW_STOCK = 'LOW STOCK';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
