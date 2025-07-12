<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Floatrates\CurrencyRateDto;
use App\ValueObject\Currency;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class FloatratesRateRepository
{
    use ResponseTransformerTrait;

    private const string URL_PATH_DAILY_RATES_TEMPLATE = '/daily/%s.json';

    public function __construct(
        private HttpClientInterface $floatratesClient,
        private CacheInterface $cache,
    ) {
    }

    /**
     * @return CurrencyRateDto[]
     */
    public function getRatesForCurrency(Currency $fromCurrency): array
    {
        try {
            $data = $this->cache->get(
                $this->generateCacheKey($fromCurrency),
                function (ItemInterface $item) use ($fromCurrency): array {
                    $item->expiresAfter(3600);

                    return $this->floatratesClient->request(
                        Request::METHOD_GET,
                        $this->getDailyRatesUrlPath($fromCurrency),
                    )->toArray();
                },
            );

            return $this->transformIntoDtoAll($data, CurrencyRateDto::class);
        } catch (ClientException $exception) {
            if ($exception->getCode() === Response::HTTP_FORBIDDEN) {
                // 403 is thrown when the currency does not exist
                return [];
            }

            throw $exception;
        }
    }

    private function getDailyRatesUrlPath(Currency $currency): string
    {
        return \sprintf(self::URL_PATH_DAILY_RATES_TEMPLATE, \strtolower($currency->value));
    }

    private function generateCacheKey(Currency $fromCurrency): string
    {
        return \sprintf('floatrates-rate-rates-for-currency-%s', $fromCurrency->value);
    }
}
