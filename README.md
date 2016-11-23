# magento2
Our new Magento2 sites

Now deploying to AWS!

## Deployment Steps

### 1) Composer Install

```
composer install --no-dev --no-progress
```

### 2) Database Install

```
php bin/magento --quiet setup:install --admin-firstname=Admin --admin-lastname=User \
--admin-email=admin@example.com --admin-user=admin.user --admin-password='k4zVlGvaQhk5U&Y5' \
--backend-frontname=limitless --db-host=127.0.0.1 --db-name=magento2 --db-user=root \
--db-password='0B2zJX6Ad^@H$aa1' --language=en_GB -s
```

### 3) JS & CSS config

```
n98-magerun2.phar config:set dev/js/merge_files 1
n98-magerun2.phar config:set dev/js/enable_js_bundling 1
n98-magerun2.phar config:set dev/js/minify_files 1
n98-magerun2.phar config:set dev/css/merge_css_files 1
n98-magerun2.phar config:set dev/css/minify_files 1
n98-magerun2.phar config:set dev/static/sign 1
```

### 4) Enable Production

```
php bin/magento --quiet deploy:mode:set production -s
```

### 5) Compile DI

```
php bin/magento --quiet setup:di:compile
```

### 6) Build Static Content

```
php bin/magento --quiet setup:static-content:deploy en_GB en_US
```

### 7) Remove config (Replaced on deployment)

```
rm app/etc/env.php
```
