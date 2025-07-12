<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Dto\Coinpaprika\MarketDto;
use App\Dto\Floatrates\CurrencyRateDto;
use Assert\Assert;

final readonly class Rate
{
    private function __construct(
        public Currency $fromCurrency,
        public Currency $toCurrency,
        public float $rate,
        public float $inverseRate,
    ) {
        Assert::that($this->rate)->min(0);
        Assert::that($this->inverseRate)->min(0);
    }

    public static function fromCoinpaprikaMarket(MarketDto $quote, Currency $toCurrency): Rate
    {
        if (!isset($quote->quotes[$toCurrency->value])) {
            throw new \InvalidArgumentException(
                \sprintf('Quote does not contain a rate for base currency "%s"',
                $toCurrency->value,
            ));
        }

        $currencyQuote = $quote->quotes[$toCurrency->value];

        return new Rate(
            $quote->pairBaseCurrency,
            $quote->pairQuoteCurrency,
            rate: $currencyQuote->price,
            inverseRate: 1 / $currencyQuote->price,
        );
    }

    public static function fromCoinpaprikaMarketInverseRate(MarketDto $quote, Currency $fromCurrency): Rate
    {
        if (!isset($quote->quotes[$fromCurrency->value])) {
            throw new \InvalidArgumentException(
                \sprintf('Quote does not contain a rate for base currency "%s"',
                    $fromCurrency->value,
                ));
        }

        $currencyQuote = $quote->quotes[$fromCurrency->value];

        return new Rate(
            $quote->pairQuoteCurrency,
            $quote->pairBaseCurrency,
            rate: 1 / $currencyQuote->price,
            inverseRate: $currencyQuote->price,
        );
    }

    public static function fromFloatratesRate(CurrencyRateDto $rate, Currency $fromCurrency): Rate
    {
        return new Rate(
            $fromCurrency,
            $rate->toCurrency,
            rate: $rate->rate,
            inverseRate: $rate->inverseRate,
        );
    }
}
