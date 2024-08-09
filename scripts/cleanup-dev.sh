# remove docker volumes
docker volume prune --all --force

# remove contents of ./data/logs
rm -rf ./data/logs/*
