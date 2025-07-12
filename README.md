This project provides 2 commands to convert one currency value into another one and to show currency rates.

The following APIs are used:

- Coinpaprika: https://api.coinpaprika.com/v1/exchanges/coinbase/markets?quotes=USD - provides market data from coinbase exchange, we can request quotes for specific markets/pairs
- Floatrates: https://www.floatrates.com/daily/usd.json - provides currency rates for general currencies

## Requirements

- PHP
- Composer
- docker
- docker-compose

## First setup

```bash
composer start
composer connect
composer install # in the container
```

## Commands

### app:currency:convert

Convert one currency value into another one.

`php bin/console app:currency:convert`

##### Options:

- `--from-currency` - `string`
- `--to-currency` - `string`

##### Arguments:

- `amount` - `float`

##### Examples:

**Example 1**

Convert 100 USD to EUR.

```bash
php bin/console app:currency:convert --from-currency=USD --to-currency=EUR 100
```

Result:

```json
{
    "amount": 85.59,
    "currency_from": {
        "rate": 1.1683717948535,
        "code": "USD"
    },
    "currency_to": {
        "rate": 1,
        "code": "EUR"
    }
}

```

**Example 2**

Convert 2.5 BTC to EUR.

```bash
php bin/console app:currency:convert --from-currency=BTC --to-currency=EUR 2.5
```

Result:

```json
{
    "amount": 249505.98,
    "currency_from": {
        "rate": 1.0019800127031025e-5,
        "code": "BTC"
    },
    "currency_to": {
        "rate": 1,
        "code": "EUR"
    }
}
```

**Example 3**

Convert 155000.4 EUR to BTC.

```bash
php bin/console app:currency:convert --from-currency=EUR --to-currency=BTC 155000.4
```

Result:

```json
{
    "amount": 1.55,
    "currency_from": {
        "rate": 99802.39,
        "code": "EUR"
    },
    "currency_to": {
        "rate": 1,
        "code": "BTC"
    }
}
```

**Example 4**

Convert 121 ETH to BTC.

```bash
php bin/console app:currency:convert --from-currency=ETH --to-currency=BTC 121
```

Result:

```json
{
    "amount": 3.02,
    "currency_from": {
        "rate": 40.06410256410256,
        "code": "ETH"
    },
    "currency_to": {
        "rate": 1,
        "code": "BTC"
    }
}
```

**Example 5**

Convert 3.21 BTC to ETH.

```bash
php bin/console app:currency:convert --from-currency=BTC --to-currency=ETH 3.21
```

Result:

```json
{
    "amount": 128.67,
    "currency_from": {
        "rate": 0.02494719,
        "code": "BTC"
    },
    "currency_to": {
        "rate": 1,
        "code": "ETH"
    }
}
```

#### app:currency:convert

Shows rates for one currency.

`php bin/console app:rate:show`

##### Options:

- `--currency` - `string`

##### Examples:

**Example 1**

```bash
php bin/console app:rate:show
```

Result:

```json
[
    // ...
    {
        "code": "BTC",
        "rate": 8.506487132151851e-6
    },
    // ...
    {
        "code": "BYN",
        "rate": 3.2725656852923
    },
    // ...
]
```

**Example 2**

```bash
php bin/console app:rate:show --currency=BTC
```

```json
[
    {
        "code": "ETH",
        "rate": 39.98400639744102
    },
    // ...
]
```

**Example 3**

```bash
php bin/console app:rate:show --currency=EUR
```

Result:

```json
[
    {
        "code": "BTC",
        "rate": 1.0015756788579794e-5
    },
    // ...
    {
        "code": "USD",
        "rate": 1.1683717948535
    },
    // ...
    {
        "code": "BYN",
        "rate": 3.8235734435008
    },
]
```

##### Known issues:

- If we want to see all prepared rates for all possible quotes from Coinpaprika, we need to provide all possible quotes, for example ?quotes=EUR,USD,... - I haven't tried this, so it's not a good idea.

## Cache

Responses from APIs are cached for 1 hour. `php bin/console cache:chear` can be used to clear this cache.
