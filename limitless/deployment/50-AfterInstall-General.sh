#!/bin/sh

DIR=/var/www/html

[ ! -d "$DIR/app" ] && echo "Not in Magento 2 root directory" >&2 && exit 1

echo "Linking the configuration to /app/etc"
# Deploy the local.xml file.
case "${DEPLOYMENT_GROUP_NAME}" in
  Staging)
    if [ ! -L ${DIR}/app/etc/env.php ]; then
      ln -s "${DIR}/limitless/env.staging.php" "${DIR}/app/etc/env.php"
    fi
    ;;
  Production)
    if [ ! -L ${DIR}/app/etc/env.php ]; then
      ln -s "${DIR}/limitless/env.production.php" "${DIR}/app/etc/env.php"
    fi
    ;;
esac

echo "Attaching Media Mount"
if [ ! -L $DIR/pub/media ]; then
  ln -s "${DIR}/limitless/config.php" "${DIR}/app/etc/config.php"
fi

echo "Attaching Media Mount"
if [ ! -L $DIR/pub/media ]; then
  ln -s "/efs/magento2/media" "${DIR}/pub/media"
fi
