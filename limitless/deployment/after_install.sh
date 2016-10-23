#!/bin/bash

DIR="$( cd "$( dirname $( dirname $( dirname "${BASH_SOURCE[0]}" ) ) )" && pwd )"

[ ! -d "$DIR/app" ] && echo "Not in Magento 2 root directory" >&2 && exit 1

echo "Moving configuration to directory"
mv "${DIR}/limitless/env.php" "${DIR}/app/etc/env.php"

echo "Setting ownership to nginx"
chown -R nginx\: /var/www/html

echo "Setting directory base permissions to 0750"
find ${DIR} -type d -exec chmod 0750 {} \;

echo "Setting file base permissions to 0640"
find ${DIR} -type f -exec chmod 0640 {} \;

echo "Making vendor/bin scripts executable"
find -L "${DIR}/vendor/bin" -type f -exec chmod u+x {} \;

echo "Making bin/magento executable"
chmod u+x "${DIR}/bin/magento"