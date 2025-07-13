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
        Assert::that($this->amount)->min(0, 'Amount can\'t be negative. Got "%s".');
    }

    public static function fromRateAndOriginAmount(Rate $rate, float $originAmount): self
    {
        return new ConvertedValue($rate, \round($originAmount * $rate->rate, precision: 2));
    }
}
