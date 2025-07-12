<?php

declare(strict_types=1);

namespace App\Dto\Floatrates;

use App\ValueObject\Currency;

final class CurrencyRateDto
{
    public Currency $toCurrency {
        get {
            return Currency::fromString($this->code);
        }
    }

    public function __construct(
        public readonly string $code,
        public readonly float $rate,
        public readonly float $inverseRate,
    ) {
    }
}
