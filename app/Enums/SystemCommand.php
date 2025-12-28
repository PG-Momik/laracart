<?php

declare(strict_types=1);

namespace App\Enums;

enum SystemCommand: string
{
    case SEND_RESTOCK_MAIL = 'app:send-restock-mail';
    case SEND_STOCK_ALERT = 'app:send-stock-alert';
    case SEND_DAILY_REPORT = 'app:send-daily-report';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function requiresEmail(): bool
    {
        return true; // For now all defined commands require email
    }
}
