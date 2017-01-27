#!/bin/bash
# Limitless Digital Group

DIR=/var/www/html

if [ "$DEPLOYMENT_GROUP_NAME" == "Worker" ]
then
    # Only run upgrade on worker server to prevent downtime
    /usr/bin/php -d memory_limit=128M $DIR/bin/magento setup:upgrade
fi