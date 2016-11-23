#!/usr/bin/env bash

MAGENTO="$( cd "$( dirname $( dirname "${BASH_SOURCE[0]}" ) )" && pwd )"

# Remove all un-tracked files except the env.php file
git clean -x -f -d --exclude="app/etc/env.php" --exclude="dev/tools/frontools/config/themes.json" --exclude=".idea" ${MAGENTO}

# Reinstall magento core from composer
(cd ${MAGENTO} && composer install)

# Check if this branch as the snowdog build tools
if [ -d "${MAGENTO}/vendor/snowdog/frontools" ]; then
    (cd "${MAGENTO}/vendor/snowdog/frontools" && npm --loglevel=silent install && gulp setup)
    sed -i '' -e '2s/  \"proxy\": \"m2test\.dev\"/  \"proxy\": \"www\.magento2\.local\"/' "${MAGENTO}/dev/tools/frontools/config/browser-sync.json"
fi