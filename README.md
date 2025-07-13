# Currency Converter

This project provides two commands to convert currency values and display exchange rates.

The following APIs are used:

APIs used:

- **Coinpaprika**: `https://api.coinpaprika.com/v1/exchanges/coinbase/markets?quotes=USD`  
  Provides cryptocurrency market data from Coinbase (with USD quote in this example).
- **Floatrates**: `https://www.floatrates.com/daily/usd.json`  
  Provides exchange rates for fiat currencies (relative to USD in this example).

## Requirements

- PHP
- Composer
- Docker
- Docker Compose

## First setup

```bash
# Start Docker containers
composer start

# Connect to the app container
composer connect

Inside container: install dependencies
composer install
```

## Commands

### app:currency:convert

Convert values between currencies.

`php bin/console app:currency:convert [--from-currency=FROM] [--to-currency=TO] AMOUNT`

##### Options:

- `--from-currency`: Source currency code (required)
- `--to-currency`: Target currency code (required)

##### Arguments:

- `amount`: Value to convert (float, required)

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

#### app:rate:show

Display exchange rates relative to a base currency.

`php bin/console app:rate:show [--currency=BASE]`

##### Options:

- `--currency`: Base currency code (default: USD)

##### Output:

Array of currencies showing how much 1 base unit equals in target currency.

##### Examples:

**Example 1**

```bash
php bin/console app:rate:show
```

Result:

```json
[
    {
        "code": "BTC",
        "rate": 8.506487132151851e-6
    },
    {
        "code": "BYN",
        "rate": 3.2725656852923
    },
    ...
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
    ...
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
    {
        "code": "USD",
        "rate": 1.1683717948535
    },
    {
        "code": "BYN",
        "rate": 3.8235734435008
    },
    ...
]
```

## Cache

- API responses cached for 1 hour
- Clear cache with: `php bin/console cache:clear`

## Known issues:

### Issue 1

When running `php bin/console app:rate:show --currency=BTC`, you won't see a USD (and some other) rate \
due to how the Coinpaprika API processes quotes:

1. **API Request Structure**:

```
# Our request to Coinpaprika
https://api.coinpaprika.com/v1/exchanges/coinbase/markets?quotes=BTC
```

2. **The Quote Paradox**:

- The `?quotes=BTC` parameter means: *"Show values of all base currencies in BTC"*
- For the BTC/USD pair:
    - Base currency = BTC
    - Quote currency = USD
- But we're requesting BTC quotes, so we get:

```json
{
  "quotes": {
    "BTC": {
      "price": 1.00030  // 1 BTC = ~1 BTC
    }
  }
}
```

3. **Why USD Disappears**:

- The API only returns the *specified quote currency* (BTC)
- USD isn't included because:
    - It's the quote currency in BTC/USD pair
    - We didn't request USD quote

4. **The Core Limitation**:

To see USD (and other) rates when BTC is the base currency, we would need to:
- Request multiple quotes: `?quotes=BTC,USD`
- Calculate inverse rates where needed

**Workaround**:  

To see BTC <-> USD rates, query from USD perspective:

```bash
php bin/console app:rate:show --currency=USD | grep BTC
```
