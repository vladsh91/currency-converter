<?php

declare(strict_types=1);

namespace App\ValueObject;

use Assert\Assert;

final readonly class Currency
{
    private function __construct(
        public string $value,
    ) {
    }

    public static function fromString(string $value): self
    {
        Assert::that($value)->notEmpty();

        return new Currency(\strtoupper($value));
    }

    public function equal(Currency $currency): bool
    {
        return $this->value === $currency->value;
    }
}
