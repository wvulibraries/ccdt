#!/bin/bash

# Setup the environment variables
cd ../src/project-css
cp .env.prod .env
docker exec -t rockefellercss_php php artisan migrate --seed
