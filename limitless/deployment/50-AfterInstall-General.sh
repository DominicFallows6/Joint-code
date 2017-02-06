#!/bin/sh

DIR=/var/www/html

[ ! -d "$DIR/app" ] && echo "Not in Magento 2 root directory" >&2 && exit 1

echo "Moving configuration to directory"
mv "${DIR}/limitless/env.php" "${DIR}/app/etc/env.php"

echo "Attaching Media Mount"
if [ ! -L $DIR/pub/media ]; then
  ln -s "/efs/magento2/media" "${DIR}/pub/media"
fi
