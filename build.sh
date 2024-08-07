#!/bin/bash

# Install necessary libraries
apt-get update && apt-get install -y libssl-dev

# Run composer install
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Optimize the application
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache