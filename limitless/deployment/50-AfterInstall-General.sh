#!/bin/sh

DIR=/var/www/html

[ ! -d "$DIR/app" ] && echo "Not in Magento 2 root directory" >&2 && exit 1

echo "Linking the configuration to /app/etc"
if [ ! -L ${DIR}/app/etc/env.php ]; then
  ln -s "/efs/magento2/env.php" "${DIR}/app/etc/env.php"
fi

echo "Attaching Media Mount"
if [ ! -L $DIR/pub/media ]; then
  ln -s "/efs/magento2/media" "${DIR}/pub/media"
fi

echo "Attaching static cache"
if [ ! -L $DIR/pub/static/_cache ]; then
  ln -s "/efs/magento2/static/_cache" "${DIR}/pub/static/_cache"
fi

echo "Attaching sitemaps EFS mount"
if [ ! -L $DIR/pub/sitemaps ]; then
  ln -s "/efs/magento2/sitemaps" "${DIR}/pub/sitemaps"
fi
