<?php

declare(strict_types=1);

namespace App\Service\RateProvider;

use App\Dto\Floatrates\CurrencyRateDto;
use App\Repository\FloatratesRateRepository;
use App\ValueObject\Currency;
use App\ValueObject\Rate;

final readonly class FloatratesRateProvider implements RateProviderInterface
{
    public function __construct(
        private FloatratesRateRepository $floatratesRateRepository,
    ) {
    }

    public function getRate(Currency $fromCurrency, Currency $toCurrency): ?Rate
    {
        $rates = $this->floatratesRateRepository->getRatesForCurrency($fromCurrency);

        $currencyRate = \array_find(
            $rates,
            static fn (CurrencyRateDto $currencyRate) => $currencyRate->toCurrency->equal($toCurrency),
        );

        if ($currencyRate === null) {
            return null;
        }

        return Rate::fromFloatratesRate($currencyRate, $fromCurrency);
    }

    public function getRates(Currency $currency): array
    {
        $rates = $this->floatratesRateRepository->getRatesForCurrency($currency);

        return \array_map(
            static fn (CurrencyRateDto $currencyRate) => Rate::fromFloatratesRate($currencyRate, $currency),
            $rates,
        );
    }
}
