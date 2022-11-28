#!/bin/bash

set -e

composer install --no-interaction
composer dump-autoload
# Clear and warm up cache
bin/console cache:clear
# Delete unneeded data before starting the container
rm -rf var/log/*
rm -rf coverage-report-*
rm -rf .php-cs-fixer.cache
rm -rf .phpunit.result.cache
# Update git pre-commit hook
cp .hooks/pre-commit .git/hooks/

php-fpm