<?php

declare(strict_types=1);

namespace App\Enums;

enum SortOrder: string
{
    case ASC = 'asc';
    case DESC = 'desc';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
