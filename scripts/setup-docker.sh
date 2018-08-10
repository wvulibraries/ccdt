#!/bin/bash

# Setup the environment variables
cd ../src/project-css
cp .env.docker .env
docker exec -t rockefeller-css_php composer update
docker exec -t rockefeller-css_php php artisan key:generate
docker exec -t rockefeller-css_php php artisan migrate --seed
