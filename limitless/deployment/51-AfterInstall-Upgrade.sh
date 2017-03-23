#!/bin/sh

DIR=/var/www/html

if [ $DEPLOYMENT_GROUP_NAME == "StagingWorker" ] || [ $DEPLOYMENT_GROUP_NAME == "ProductionWorker" ]; then
    # Only run upgrade on worker server to prevent downtime
    /usr/bin/php $DIR/bin/magento setup:upgrade --keep-generated
fi
