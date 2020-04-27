#!/bin/bash
cd ../src/project-css
# Setup the environment variables
cp .env.dev .env
# update composer packages
docker exec -t ccdt_php composer update
# update node package manager
docker exec -t ccdt_php npm install
# generate new application key
docker exec -t ccdt_php php artisan key:generate
# setup main database
docker exec -t ccdt_php php artisan migrate --seed --database=mysql
# update the loaded enviromental
docker exec -t ccdt_php php artisan config:clear