#!/bin/bash

# Build the app
composer install

# Run PHPStan
vendor/bin/phpstan analyze

# Run cs-checker
vendor/bin/phpcs --standard=PSR2 public/

# Run tests
vendor/bin/phpunit tests/

