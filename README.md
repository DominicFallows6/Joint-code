# magento2
Our new Magento2 sites

Now deploying to AWS!

## Deployment Steps

### 1) Composer Install

```
composer install --no-dev --no-progress
```

### 2) Core Hack - Permission from Vinai

```
sed -i'tmp' 's/\$hasCustomization = false;/\$hasCustomization = true;/' vendor/magento/module-deploy/Model/DeployStrategyProvider.php
sed -i'tmp' 's/foreach (\$this->getCustomizationDirectories(\$area, \$themePath, \$locale) as $directory)/if(false)/' vendor/magento/module-deploy/Model/DeployStrategyProvider.php
```

### 3) Copy core config to app/etc/

```
cp limitless/config.local.php app/etc/config.local.php
```

### 4) Compile DI

```
php bin/magento setup:di:compile
```

### 5) Build Static Content

```
php bin/magento setup:static-content:deploy en_GB en_US es_ES de_DE
```

### 6) Cleanup before deployment

```
rm app/etc/env.php
rm app/etc/config.local.php
rm var/session -Rf
rm pub/media -Rf
```
