# Simple Currency tracking app
## Description

Currency tracking app based on Symfony 7.3 + RabbitMQ + Docker

## Getting Started

### Dependencies

* FREE_CURRENCY_API_KEY from https://freecurrencyapi.com/

### Installing

* clone repo to your local machine
* set FREE_CURRENCY_API_KEY in .env
* on project root run command via console
```
docker compose build --no-cache
docker compose up -d --force-recreate
docker exec -it currency-tracking-php bash
composer install
# execute migrations
php bin/console doctrine:migrations:migrate
```
* optional. run command to load fixtures
```
php bin/console doctrine:fixtures:load
```

* App available on  http://localhost:8080/ 
* RabbitMQ available on http://localhost:15672/

### How to use
### Command 'exchange:pair'
* run command
```
php bin/console exchange:pair
```
* Enter Base and Target currency
> Please enter the base currency:  
> Please enter the target currency:

* Select action (add, remove, history)
> Please select action ("add" by default)  
>   [0] add  
>   [1] remove  
>   [2] history

### Command 'exchange:pair:track'
Command track currency pair rates.
Command triggers by "symfony scheduler".
To enable tracking follow these steps:
* Run commands in separate terminals
```
php bin/console messenger:consume async -vv
php bin/console messenger:consume scheduler_default -vv
```

### Endpoint '/api/exchange-rate'

- **`base`** (`string`, required): Base currency code (e.g. `EUR`)
- **`target[]`** (`array`, required): One or more target currency codes (e.g. `CAD`, `GBP`)
- **`at`** (`string`, required): Datetime in `YYYY-MM-DDTHH:MM:SS` format (e.g. `2025-07-06T15:12:01`)  
  Precision is up to seconds. The search matches records with exact datetime.
```
'GET /api/exchange-rate?base=EUR&target[]=CAD&target[]=GBP&&at=2025-07-06T15:12:01

{
  "items": [
    {
      "base": "EUR",
      "target": "CAD",
      "rate": "1.6040942474",
      "date": "2025-07-06 15:12:01"
    },
    {
      "base": "EUR",
      "target": "GBP",
      "rate": "0.8636668761",
      "date": "2025-07-06 15:12:01"
    }
  ]
}
```

#### Tips & Tricks
* create migration
```
php bin/console make:migration
```
* execute migrations
```
php bin/console doctrine:migrations:migrate
```
