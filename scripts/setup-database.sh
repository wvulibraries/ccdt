#!/bin/bash

docker exec -t ccdt_php php artisan key:generate
docker exec -t ccdt_php php artisan migrate --seed
