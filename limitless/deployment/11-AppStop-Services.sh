#!/bin/sh

rm /var/www/html/* -Rf
service nginx stop

if [ $DEPLOYMENT_GROUP_NAME == "StagingWorker" ] || [ $DEPLOYMENT_GROUP_NAME == "ProductionWorker" ]; then
  service crond stop
fi
