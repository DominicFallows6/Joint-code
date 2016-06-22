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
composer require <product> 2.0.7 --no-update
composer update
```

- To upgrade to Magento EE 2.0.7, enter:

```
composer require magento/product-enterprise-edition 2.0.7 --no-update
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