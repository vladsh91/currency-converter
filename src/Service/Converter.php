<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\RateQuery\RateQuery;
use App\ValueObject\ConvertedValue;
use App\ValueObject\Currency;

final readonly class Converter
{
    public function __construct(
        private RateQuery $rateQuery,
    ) {

    }

    public function convert(Currency $fromCurrency, Currency $toCurrency, float $amount): ?ConvertedValue
    {
        $rate = $this->rateQuery->query($fromCurrency, $toCurrency);

        if ($rate === null) {
            return null;
        }

        return ConvertedValue::fromRateAndAmount($rate, \round($amount * $rate->rate, precision: 2));
    }
}

