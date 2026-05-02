#!/bin/bash

set -ex

cd "$(dirname "$0")"

./docker/wait-for database:3306 -t 120

if [ "$1" == "dev" ]
then
    composer install
    [ -f .env ] || { cp .env.example .env && php artisan key:generate --no-ansi ; }
fi

php artisan migrate --force
php artisan store:import
php artisan product:import
php artisan product:quantity
php artisan offer:import r001
php artisan offer:import r002
php artisan offer:import r003

exec php -S 0.0.0.0:8000 -t public/
