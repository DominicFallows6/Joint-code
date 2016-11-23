#!/bin/bash

DIR=/var/www/html

/usr/bin/php -d memory_limit=128M $DIR/bin/magento setup:upgrade