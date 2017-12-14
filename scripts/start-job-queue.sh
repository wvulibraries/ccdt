#!/bin/bash

docker exec -t rockefellercss_php nohup php artisan queue:work --daemon --sleep=3 --tries=3 > /dev/null &
