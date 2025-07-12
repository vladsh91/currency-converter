<?php

declare(strict_types=1);

namespace App\Dto\Coinpaprika;

final readonly class CurrencyQuoteDto
{
    public function __construct(
        public float $price,
    ) {
    }
}
