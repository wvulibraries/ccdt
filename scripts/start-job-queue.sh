#!/bin/bash

docker exec -t rockefeller-css_php nohup php artisan queue:work --daemon --sleep=3 --tries=3 > /dev/null &
