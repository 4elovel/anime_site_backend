<?php

namespace AnimeSite\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    public function name(): string
    {
        return match ($this) {
            self::PENDING => 'В очікуванні',
            self::SUCCESS => 'Успішно',
            self::FAILED => 'Невдало',
            self::REFUNDED => 'Повернуто',
        };
    }

    public function getBadgeColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::SUCCESS => 'success',
            self::FAILED => 'danger',
            self::REFUNDED => 'info',
        };
    }

}
