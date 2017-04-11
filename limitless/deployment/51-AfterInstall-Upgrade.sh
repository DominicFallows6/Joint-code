#!/bin/sh

DIR=/var/www/html

if [ $DEPLOYMENT_GROUP_NAME == "StagingWorker" ] || [ $DEPLOYMENT_GROUP_NAME == "ProductionWorker" ]; then
    # Only run upgrade on worker server to prevent downtime
    /usr/bin/php $DIR/bin/magento setup:upgrade
    # bin/magento setup:upgrade was previously run with --keep-generated option. No longer required now we're running this 
    # on the Worker server as there is no FE dependency on the static content which is deleted without it. 
    # This option has been removed from this script as it caused unexpected results in some releases
    # causing the upgrade to fail
fi
