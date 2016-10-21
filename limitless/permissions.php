#!/bin/bash
#
# This script can be used to correct all the directory permissions for Magento 2. Please ensure that you, nginx, and
# php-fpm all use the same group (staff) to ensure that permissions are maintained.
# Based on work by Vinai Kopp - https://gist.github.com/Vinai/69dc72b9f4baa2506120

DIR="$( cd "$( dirname $( dirname "${BASH_SOURCE[0]}" ) )" && pwd )"

[ ! -d "$DIR/app" ] && echo "Not in Magento 2 root directory" >&2 && exit 1

echo "Setting group ownership to staff"
chgrp -R staff $DIR

echo "Setting directory base permissions to 0750"
find $DIR -type d -exec chmod 0750 {} \;
echo "Setting file base permissions to 0640"
find $DIR -type f -exec chmod 0640 {} \;

echo "Setting group write and sgid bit permissions for writable directories"
find "$DIR/var" "$DIR/pub/media" "$DIR/pub/static" -type d -exec chmod g+ws {} \;
echo "Setting permissions for files in writable directories "
find "$DIR/var" "$DIR/pub/media" "$DIR/pub/static" -type f -not -name .htaccess -exec chmod g+w {} \;

[ -e "$DIR/app/etc" ] && echo "Making app/etc writable for installation"
[ -e "$DIR/app/etc" ]  && chmod g+w "$DIR/app/etc"

echo "Making vendor/bin scripts executable"
find -L "$DIR/vendor/bin" -type f -exec chmod u+x {} \;

echo "Making bin/magento executable"
chmod u+x "$DIR/bin/magento"
