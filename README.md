# Invoicing API challenge

The description of the challenge is at https://github.com/clippings/documents-calculation-challenge (28/11/2022)

## Supported platforms:

* Linux
* MacOS
* Windows 10

## Prerequisites

* Windows 10:
    * WSL2 installed with Ubuntu 20.04 image
        * git
        * make
        * bash
    * Docker-desktop with wsl2 configured.
* Linux and MacOS:
    * Docker/Docker-desktop
    * git
    * make
    * bash

## Setup

1. Clone project
2. Update `UID and GID` env variables if you use Mac or native Linux (Run `id` in host machine terminal to see user id
   and gid)
3. Start project `make start`
4. Open http://localhost:8088 and make a test requests (You can test directly the api using postman/curl or other tool
   on http://localhost:8080/api/v1/sumInvoices)

## Additional commands:

1. `make` - lists all available commands
2. `make stop` - stop project
3. `make down` - remove docker containers and network
4. `make logs` - view php container logs
5. `make terminal` - open terminal
6. `make terminal-root` - open terminal as root user
7. `make clear-cache` - clear cache
8. `make format` - run php cs fixer
9. `make phpstan` - run static code analyser
10. `make security-check` - run security checker
11. `make run-tests` - run all tests
12. `make unit-tests` - run all tests
13. `make integration-tests` - run integration tests
14. `make functional-tests` - run functional tests

## Coverage report

1. Run `make run-tests`  to generate html coverage report
2. Open `coverage-report-html/index.html` in browser of your choice

## Debugging

Symfony web profiler cn be found at http://localhost:8080/_profiler/

## Improvements

### If I had more time I would:
* write more tests (validator, persistence, etc.)
* add custom exceptions and an error subscriber
* make better error messages

### Business logic:
In real world scenario I would split the business logic in multiple parts/apis and would use REST.
Here's an example to illustrate my words:

1. Exchange rates:
* The default currency would be always the same e.g. EUR.
* All supported currencies (with their exchange rates) would be stored in a db/other persistence storage.
* API for updating exchange rates - `PUT /api/v1/exchange-rates` with json body which accepts array of {"code": "STRING", "rate": "FLOAT"} and return 204 (No content)
Depending on the business case instead of API it can be a cron job using external service for fetching exchange rates, or message subscriber.
2. For storing invoice information about customers and invoice documents I would create several API endpoints:
* `POST /api/v1/customers` {"vatNumber": "STRING", "name": "STRING"} which will return 201
* `POST /api/v1/customers/{VAT_NUMBER}/invoices` which will return 201
* `POST /api/v1/customers/{VAT_NUMBER}/invoices/{INVOICE_NUMBER}` which will return 201
* `POST /api/v1/customers/{VAT_NUMBER}/invoices/{INVOICE_NUMBER}/credit-notes` which will return 201
* `POST /api/v1/customers/{VAT_NUMBER}/invoices/{INVOICE_NUMBER}/debit-notes` which will return 201
* `POST /api/v1/imports/invoice-documents` which accepts only csv file and returns 200
3. For fetching data I would create the following endpoints
* `GET /api/v1/customers/{VAT_NUMBER}/balances/{OUTPUT_CURRENCY}` which will return 200 (only the calculated balance)
* `GET /api/v1/balances/{OUTPUT_CURRENCY}` which will return 200 (only an array of customer name/vatNumber and calculated balance)