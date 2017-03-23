#!/bin/sh

service nginx start

if [ $DEPLOYMENT_GROUP_NAME == "StagingWorker" ] || [ $DEPLOYMENT_GROUP_NAME == "ProductionWorker" ]; then
    service crond start
fi
