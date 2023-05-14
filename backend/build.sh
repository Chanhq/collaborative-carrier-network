#!/bin/bash

# Build the app
composer install

# Run PHPStan
vendor/bin/phpstan analyze

# Run cs-checker
vendor/bin/phpcs --standard=PSR2 src/

# Run tests
vendor/bin/phpunit tests/

# Run linter
vendor/bin/php-cs-fixer fix --dry-run --diff
