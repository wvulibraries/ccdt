#!/bin/bash

# Setup the environment variables
cd ../src/project-css
cp .env.docker .env
docker exec -t rockefellercss_php composer update
docker exec -t rockefellercss_php php artisan key:generate
docker exec -t rockefellercss_php php artisan migrate --seed
