<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Coinpaprika\MarketDto;
use App\ValueObject\Currency;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class CoinpaprikaCoinbaseMarketRepository
{
    use ResponseTransformerTrait;

    private const string URL_PATH_EXCHANGES_MARKETS = '/v1/exchanges/coinbase/markets';

    public function __construct(
        private HttpClientInterface $coinpaprikaClient,
        private CacheInterface $cache,
    ) {
    }

    /**
     * @param Currency[] $quoteCurrencies
     *
     * @return MarketDto[]
     */
    public function getMarketsWithCurrencyQuotes(array $quoteCurrencies): array
    {
        $quoteValues = \implode(
            ',',
            \array_map(static fn (Currency $currency) => $currency->value, $quoteCurrencies),
        );

        try {
            $data = $this->cache->get(
                $this->generateCacheKey($quoteValues),
                function (ItemInterface $item) use ($quoteValues): array {
                    $item->expiresAfter(3600);

                    return $this->coinpaprikaClient->request(
                        Request::METHOD_GET,
                        self::URL_PATH_EXCHANGES_MARKETS,
                        ['query' => ['quotes' => $quoteValues]],
                    )->toArray();
                }
            );

            return $this->transformIntoDtoAll($data, MarketDto::class);
        } catch (ClientException $exception) {
            if ($exception->getCode() === Response::HTTP_BAD_REQUEST) {
                // 400 is thrown when the quote currency does not exist
                return [];
            }

            throw $exception;
        }
    }

    private function generateCacheKey(string $quoteValues): string
    {
        return \sprintf('coinpaprika-coinbase-markets-for-quote-currencies-%s', $quoteValues);
    }
}
