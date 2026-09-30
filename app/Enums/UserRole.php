<?php

namespace App\Enums;

enum UserRole: string
{
    case OWNER = 'owner';
    case FRONT_DESK = 'front_desk';
    case TECHNICIAN = 'technician';

    public function label(): string
    {
        return match ($this) {
            self::OWNER => 'صاحب الورشة',
            self::FRONT_DESK => 'موظف الاستقبال',
            self::TECHNICIAN => 'فني / عامل',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
