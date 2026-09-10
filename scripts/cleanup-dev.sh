#!/bin/bash
# Cleanup development environment
# Usage: cleanup-dev.sh [--full]
#   --full  Also clear vendor/ (forces composer reinstall on next setup)

# remove docker volumes (database, etc.)
docker volume prune --all --force

# clear test run artifacts
rm -rf ./data/exports/*
rm -rf ./data/logs/*
rm -rf ./data/flatfiles/*

# optionally clear vendor for full dependency reset
if [ "$1" = "--full" ]; then
    echo "Clearing vendor/ (full reset)..."
    rm -rf ./data/vendor/*
else
    echo "Preserving vendor/ (use --full flag to clear)"
fi
