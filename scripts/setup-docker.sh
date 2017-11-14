#!/bin/bash

# Setup the environment variables
cp ../src/project-css/.env.docker ../src/project-css/.env
docker exec -t rockerfeller_css_php composer update
docker exec -t rockerfeller_css_php php artisan key:generate
docker exec -t rockerfeller_css_php php artisan migrate --seed
