#!/bin/bash

# Setup the environment variables
docker exec -t rockefellercss_php php artisan migrate --seed
