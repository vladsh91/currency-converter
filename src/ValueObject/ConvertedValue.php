<?php

declare(strict_types=1);

namespace App\ValueObject;

use Assert\Assert;

final readonly class ConvertedValue
{
    private function __construct(
        public Rate $rate,
        public float $amount,
    ) {
        Assert::that($this->amount)->min(0);
    }

    public static function fromRateAndAmount(Rate $rate, float $amount): self
    {
        return new ConvertedValue($rate, $amount);
    }
}
