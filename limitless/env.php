<?php
return array(
    'cache_types' =>
        array(
            'compiled_config' => 1,
            'config' => 1,
            'layout' => 1,
            'block_html' => 1,
            'collections' => 1,
            'reflection' => 1,
            'db_ddl' => 1,
            'eav' => 1,
            'customer_notification' => 1,
            'target_rule' => 1,
            'full_page' => 1,
            'config_integration' => 1,
            'config_integration_api' => 1,
            'translate' => 1,
            'config_webservice' => 1,
        ),
    'backend' =>
        array(
            'frontName' => 'limitless',
        ),
    'db' =>
        array(
            'connection' =>
                array(
                    'indexer' =>
                        array(
                            'host' => 'magento2-staging-cluster.cluster-cxwxfaziwde9.eu-west-1.rds.amazonaws.com',
                            'dbname' => 'magento2',
                            'username' => 'webserver',
                            'password' => '!ZA1mD6xY^EScJcD',
                            'active' => '1',
                            'persistent' => NULL,
                        ),
                    'default' =>
                        array(
                            'host' => 'magento2-staging-cluster.cluster-cxwxfaziwde9.eu-west-1.rds.amazonaws.com',
                            'dbname' => 'magento2',
                            'username' => 'webserver',
                            'password' => '!ZA1mD6xY^EScJcD',
                            'active' => '1',
                        ),
                ),
            'table_prefix' => '',
        ),
    'cache' =>
        array(
            'frontend' =>
                array(
                    'default' =>
                        array(
                            'backend' => 'Cm_Cache_Backend_Redis',
                            'backend_options' =>
                                array(
                                    'server' => 'central.tyrt1z.ng.0001.euw1.cache.amazonaws.com',
                                    'port' => '6379',
                                ),
                        ),
                    'page_cache' =>
                        array(
                            'backend' => 'Cm_Cache_Backend_Redis',
                            'backend_options' =>
                                array(
                                    'server' => 'page.tyrt1z.ng.0001.euw1.cache.amazonaws.com',
                                    'port' => '6379',
                                    'compress_data' => '0'
                                )
                        )
                )
        ),
    'crypt' =>
        array(
            'key' => '9aa17b9681659942c0276caeb60d8d1b',
        ),
/*    'session' =>
        array(
            'save' => 'memcached',
            'save_path' => 'sessions.tyrt1z.cfg.euw1.cache.amazonaws.com:11211',
        ),*/
    'session' =>
        array(
            'save' => 'db'
        ),
    'resource' =>
        array(
            'default_setup' =>
                array(
                    'connection' => 'default',
                ),
        ),
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'production',
    'install' =>
        array(
            'date' => 'Sat, 22 Oct 2016 19:30:47 +0000',
        ),
);
