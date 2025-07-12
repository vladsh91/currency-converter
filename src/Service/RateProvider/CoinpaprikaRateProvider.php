<?php

declare(strict_types=1);

namespace App\Service\RateProvider;

use App\Dto\Coinpaprika\MarketDto;
use App\Repository\CoinpaprikaCoinbaseMarketRepository;
use App\ValueObject\Currency;
use App\ValueObject\Rate;

final readonly class CoinpaprikaRateProvider implements RateProviderInterface
{
    public function __construct(
        private CoinpaprikaCoinbaseMarketRepository $coinpaprikaCoinbaseMarketRepository,
    ) {
    }

    public function getRate(Currency $fromCurrency, Currency $toCurrency): ?Rate
    {
        $markets = $this->coinpaprikaCoinbaseMarketRepository->getMarketsWithCurrencyQuotes([$fromCurrency, $toCurrency]);
        $market = \array_find($markets, static function (MarketDto $market) use ($fromCurrency, $toCurrency): bool {
            return $market->pairBaseCurrency->equal($fromCurrency)
                && $market->pairQuoteCurrency->equal($toCurrency);
        });

        if ($market !== null) {
            return Rate::fromCoinpaprikaMarket($market, $toCurrency);
        }
        $market = \array_find($markets, static function (MarketDto $market) use ($fromCurrency, $toCurrency): bool {
            return $market->pairBaseCurrency->equal($toCurrency)
                && $market->pairQuoteCurrency->equal($fromCurrency);
        });

        if ($market !== null) {
            return Rate::fromCoinpaprikaMarketInverseRate($market, $fromCurrency);
        }

        return null;
    }

    public function getRates(Currency $currency): array
    {
        $markets = $this->coinpaprikaCoinbaseMarketRepository->getMarketsWithCurrencyQuotes([$currency]);

        return \array_map(
            static fn (MarketDto $market) => Rate::fromCoinpaprikaMarketInverseRate($market, $currency),
            \array_filter($markets, static fn (MarketDto $market) => $market->pairQuoteCurrency->equal($currency)),
        );
    }
}
