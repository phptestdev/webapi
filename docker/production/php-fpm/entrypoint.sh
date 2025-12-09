#!/bin/bash
cd /var/www/html

if [ ! -d "vendor" ]; then
    composer install
fi

exec "$@"
