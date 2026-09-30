<?php

namespace App\Enums;

enum JobOrderStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case WAITING_PARTS = 'waiting_parts';
    case READY = 'ready';
    case DELIVERED = 'delivered';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'مستنية',
            self::IN_PROGRESS => 'جاري الشغل',
            self::WAITING_PARTS => 'مستنية قطعة غيار',
            self::READY => 'جاهزة للتسليم',
            self::DELIVERED => 'تم التسليم',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
