#!/bin/bash
# remove docker volumes (database, etc.)
docker volume prune --all --force
# clear test run artifacts but preserve raw import data (files/, flatfiles/)
rm -rf ./data/exports/*
rm -rf ./data/logs/*
rm -rf ./data/vendor/*
