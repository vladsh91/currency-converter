<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Dto\Coinpaprika\CurrencyQuoteDto;
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
        Assert::that($this->rate)->min(0, 'Rate can\'t be negative. Got "%s".');
        Assert::that($this->inverseRate)->min(0, 'Inverse rate can\'t be negative. Got "%s".');
    }

    public static function fromCoinpaprikaMarket(MarketDto $market, Currency $toCurrency): Rate
    {
        $currencyQuote = self::validateCurrencyQuote($market, $toCurrency);

        return new Rate(
            fromCurrency: $market->pairBaseCurrency,
            toCurrency: $market->pairQuoteCurrency,
            rate: $currencyQuote->price,
            inverseRate: 1 / $currencyQuote->price,
        );
    }

    public static function fromCoinpaprikaMarketInverseRate(MarketDto $market, Currency $fromCurrency): Rate
    {
        $currencyQuote = self::validateCurrencyQuote($market, $fromCurrency);

        return new Rate(
            fromCurrency: $market->pairQuoteCurrency,
            toCurrency: $market->pairBaseCurrency,
            rate: 1 / $currencyQuote->price,
            inverseRate: $currencyQuote->price,
        );
    }

    public static function fromFloatratesRate(CurrencyRateDto $rate, Currency $fromCurrency): Rate
    {
        return new Rate(
            fromCurrency: $fromCurrency,
            toCurrency: $rate->toCurrency,
            rate: $rate->rate,
            inverseRate: $rate->inverseRate,
        );
    }

    private static function validateCurrencyQuote(MarketDto $market, Currency $currency): CurrencyQuoteDto
    {
        if (!isset($market->quotes[$currency->value])) {
            throw new \InvalidArgumentException(
                \sprintf('Market "%s" does not contain quote for "%s" currency.', $market->pair, $currency->value),
            );
        }

        return $market->quotes[$currency->value];
    }
}
