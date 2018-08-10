#!/bin/bash

# Setup the environment variables
docker exec -t rockefeller-css_php php artisan migrate --seed
