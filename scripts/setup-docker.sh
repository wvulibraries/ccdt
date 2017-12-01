#!/bin/bash

# Setup the environment variables
cp ../src/project-css/.env.docker ../src/project-css/.env
docker exec -t rockefellercss_php composer update
docker exec -t rockefellercss_php php artisan key:generate
docker exec -t rockefellercss_php php artisan migrate --seed
docker exec -t rockefellercss_php nohup php artisan queue:work --daemon --sleep=3 --tries=3 > /dev/null &
