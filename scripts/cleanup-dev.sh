#!/bin/bash
# remove docker volumes
docker volume prune --all --force
# remove contents of ./data
rm -rf ./data/*