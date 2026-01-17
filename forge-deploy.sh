#!/bin/bash
set -e

echo "🚀 Deploy iniciado..."

cd /home/gopublicom/api.gopubli.com.br

# Pull latest changes
git pull origin main

# Install/update composer dependencies
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Install/update npm dependencies and build
npm ci
npm run build

# Run migrations
php artisan migrate --force

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link if needed
php artisan storage:link || true

# Restart queue workers if you use them
# php artisan queue:restart

echo "✅ Deploy concluído!"
