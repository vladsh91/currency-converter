<?php

declare(strict_types=1);

namespace App\Dto\Coinpaprika;

use App\ValueObject\Currency;
use Assert\Assert;

final class MarketDto
{
    public Currency $pairBaseCurrency {
        get {
            return Currency::fromString($this->pairArray['baseCurrency']);
        }
    }

    public Currency $pairQuoteCurrency {
        get {
            return Currency::fromString($this->pairArray['quoteCurrency']);
        }
    }

    private array $pairArray {
        get {
            $array = \explode('/', $this->pair);
            Assert::that($array)->count(2);

            return [
                'baseCurrency' => $array[0],
                'quoteCurrency' => $array[1],
            ];
        }
    }

    public function __construct(
        public readonly string $pair,
        /** @var array<string, CurrencyQuoteDto> */
        public readonly array $quotes,
    ) {
    }
}
