#!/bin/bash

cd scripts
./pull-docker-images.sh
cd ..
docker-compose up
