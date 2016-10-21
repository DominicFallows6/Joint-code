## To upgrade to Magento EE using the command line:

- Log in to your Magento server as, or switch to, the Magento file system owner.
- Change to the directory in which you installed the Magento software.
For example, `cd /var/www/html/magento2`

- Enter the following command to disable the cache:

```
php bin/magento cache:disable
```

- Enter the following commands in the order shown:

```
composer require <product> <version> --no-update
composer update
```

- To upgrade to Magento EE <version>, enter:

```
composer require magento/product-enterprise-edition <version> --no-update
composer update
```

- If prompted, enter your authentication keys.
- Update the database schema and data:

```
php bin/magento setup:upgrade
```

- Enter the following command to enable the cache:

```
php bin/magento cache:enable
```

## Useful Aliases ##

```
alias php-restart='sudo /usr/local/opt/php70/sbin/php70-fpm restart'
alias clear-cache='rm -rfv /var/www/html/magento2/var/generation/*; rm -rfv /var/www/html/magento2/var/cache/*'
```
