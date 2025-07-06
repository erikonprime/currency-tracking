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
composer install
php bin/console doctrine:migrations:migrate
```
* App available on  http://localhost:8080/ 
* RabbitMQ available on http://localhost:15672/
* optional. run command to load fixtures
```
php bin/console doctrine:fixtures:load
```

### How to use
### Command 'exchange:pair'
* run command
```
php bin/console exchange:pair
```
* Enter Base and Target currency
```
Please enter the base currency:
Please enter the target currency:
```
* Select action (add, remove, history)
```
Please select action ("add" by default)
  [0] add
  [1] remove
  [2] history
```

### Command 'exchange:pair:track'
Command track currency pair rates.
Command triggers by "symfony scheduler".
To enable tracking follow these steps:
* Run commands in separate terminals
```
php bin/console messenger:consume async -vv
php bin/console messenger:consume scheduler_default -vv
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
