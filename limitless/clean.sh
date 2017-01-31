#!/usr/bin/env bash

MAGENTO="$( cd "$( dirname $( dirname "${BASH_SOURCE[0]}" ) )" && pwd )"

# Remove all un-tracked files except the env.php file
git clean -x -f -d --exclude="app/etc/env.php" --exclude="dev/tools/frontools/config/themes.json" --exclude=".idea" ${MAGENTO}

# Reinstall magento core from composer
(cd ${MAGENTO} && composer install)