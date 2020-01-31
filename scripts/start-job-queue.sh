#!/bin/bash

docker exec -t rockefeller-css_php nohup php artisan queue:work --daemon --timeout=86400 > /dev/null &
