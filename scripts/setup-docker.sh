#!/bin/bash

# Setup the environment variables
cd ../src/project-css
cp .env.docker .env

docker exec -t rockefellercss_app_1 composer update
docker exec -t rockefellercss_app_1 php artisan key:generate
docker exec -t rockefellercss_app_1 php artisan migrate --seed
