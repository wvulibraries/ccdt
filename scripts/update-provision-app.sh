#!/bin/bash

# Setup the environment variables
cd ../src/project-css
cp .env.docker .env
# update composer packages
docker exec -t ccdt_php composer update
# update node package manager
docker exec -t ccdt_php npm i -g npm-check-updates && ncu -u && npm i
docker exec -t ccdt_php php artisan key:generate
docker exec -t ccdt_php php artisan migrate --seed
