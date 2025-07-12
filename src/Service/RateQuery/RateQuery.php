<?php

declare(strict_types=1);

namespace App\Service\RateQuery;

use App\Service\RateProvider\RateProviderInterface;
use App\ValueObject\Currency;
use App\ValueObject\Rate;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class RateQuery
{
    public function __construct(
        /** @var RateProviderInterface[] */
        #[AutowireIterator('rate.providers')]
        private \Traversable $rateProviders,
    ) {
    }

    public function query(Currency $fromCurrency, Currency $toCurrency): ?Rate
    {
        foreach ($this->rateProviders as $rateProvider) {
            $rate = $rateProvider->getRate($fromCurrency, $toCurrency);

            if ($rate !== null) {
                return $rate;
            }
        }

        return null;
    }

    public function all(Currency $currency): array
    {
        $rates = [];

        foreach ($this->rateProviders as $rateProvider) {
            $rates[] = $rateProvider->getRates($currency);
        }

        return \array_merge(...$rates);
    }
}
