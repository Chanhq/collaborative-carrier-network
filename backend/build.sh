#!/bin/bash

# Install dependencies
composer install

# Build the application
php bin/console cache:clear --env=prod --no-debug

# Run PHPStan
vendor/bin/phpstan analyze src --level=5

# Run cs-checker
vendor/bin/phpcs src --standard=PSR2

# Run tests
vendor/bin/phpunit tests

# Run linter
vendor/bin/phpcs src --standard=PSR2