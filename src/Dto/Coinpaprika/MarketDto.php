<?php

declare(strict_types=1);

namespace App\Dto\Coinpaprika;

use App\ValueObject\Currency;
use Assert\Assert;

final class MarketDto
{
    private const int PAIR_INDEX_BASE_CURRENCY = 0;
    private const int PAIR_INDEX_QUOTE_CURRENCY = 1;
    private const string PAIR_DELIMITER = '/';

    public Currency $pairBaseCurrency {
        get {
            return Currency::fromString($this->pairArray[self::PAIR_INDEX_BASE_CURRENCY]);
        }
    }

    public Currency $pairQuoteCurrency {
        get {
            return Currency::fromString($this->pairArray[self::PAIR_INDEX_QUOTE_CURRENCY]);
        }
    }

    private array $pairArray {
        get {
            $array = \explode(self::PAIR_DELIMITER, $this->pair);

            Assert::that($array)->count(2, 'Pair must have 2 currencies. Got "%s".');
            Assert::thatAll($array)->notEmpty('Pair currencies can\'t be empty. Got "%s".');

            return $array;
        }
    }

    public function __construct(
        public readonly string $pair,
        /** @var array<string, CurrencyQuoteDto> */
        public readonly array $quotes,
    ) {
    }
}
