# Installation
```sh
composer install
```

## Configuration
copy file `config.local.yml.temp` => `config.local.yml` and edit database parameters

## DB
sql from task/make_database.sql

## Tests
```sh
php vendor/bin/codeception run
```

## Run!
```sh
php index.php
```

Options:

```
  --file=FILE           Path to import file [default: "task/stock.csv"]
  -t, --test            Test mode does not inserting anything into DB

```

more info after: `php index.php --help`