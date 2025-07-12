<?php

declare(strict_types=1);

namespace App\Service\RateProvider;

use App\ValueObject\Currency;
use App\ValueObject\Rate;

interface RateProviderInterface
{
    public function getRate(Currency $fromCurrency, Currency $toCurrency): ?Rate;

    /**
     * @return Rate[]
     */
    public function getRates(Currency $currency): array;
}
