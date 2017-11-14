#!/bin/bash

# stop all containers
docker kill $(docker ps -q)

#remove all containers
docker rm $(docker ps -a -q)

#remove all docker images
docker rmi $(docker images -q)

# clear mysql for testing
#rm -r /Users/tracymccormick/Documents/git/server-docker-testing/mysql_data
#rm -r /Users/tracymccormick/Documents/git/server-docker-testing/data
