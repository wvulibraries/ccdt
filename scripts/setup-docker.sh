#!/bin/bash

# Setup the environment variables
cp ../src/project-css/.env.docker ../src/project-css/.env
docker exec -t rockefellercss_app_1 composer update
docker exec -t rockefellercss_app_1 php artisan key:generate
docker exec -t rockefellercss_app_1 php artisan migrate --seed