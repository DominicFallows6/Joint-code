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
                            'host' => 'magento2-production-cluster.cluster-cxwxfaziwde9.eu-west-1.rds.amazonaws.com',
                            'dbname' => 'magento2',
                            'username' => 'webserver',
                            'password' => '1l7%p49*DkW1eM$q',
                            'active' => '1',
                            'persistent' => NULL,
                            'model' => 'mysql4',
                            'engine' => 'innodb',
                            'initStatements' => 'SET NAMES utf8;',
                        ),
                    'default' =>
                        array(
                            'host' => 'magento2-production-cluster.cluster-cxwxfaziwde9.eu-west-1.rds.amazonaws.com',
                            'dbname' => 'magento2',
                            'username' => 'webserver',
                            'password' => '1l7%p49*DkW1eM$q',
                            'active' => '1',
                            'model' => 'mysql4',
                            'engine' => 'innodb',
                            'initStatements' => 'SET NAMES utf8;',
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
                                    'server' => 'm2p-main.tyrt1z.ng.0001.euw1.cache.amazonaws.com',
                                    'port' => '6379',
                                ),
                        ),
                    'page_cache' =>
                        array(
                            'backend' => 'Cm_Cache_Backend_Redis',
                            'backend_options' =>
                                array(
                                    'server' => 'm2p-page.tyrt1z.ng.0001.euw1.cache.amazonaws.com',
                                    'port' => '6379',
                                    'compress_data' => '0'
                                )
                        )
                )
        ),
    'crypt' =>
        array(
            'key' => 'ede45f6840a08c73e9fc87a111991b8f',
        ),
    'session' => 
        array (
            'save' => 'redis',
            'redis' => 
                array (
                    'host' => 'm2p-session.tyrt1z.ng.0001.euw1.cache.amazonaws.com',
                    'port' => '6379',
                    'password' => '',
                    'timeout' => '2.5',
                    'persistent_identifier' => '',
                    'database' => '0',
                    'compression_threshold' => '2048',
                    'compression_library' => 'gzip',
                    'log_level' => '1',
                    'max_concurrency' => '6',
                    'break_after_frontend' => '5',
                    'break_after_adminhtml' => '30',
                    'first_lifetime' => '600',
                    'bot_first_lifetime' => '60',
                    'bot_lifetime' => '7200',
                    'disable_locking' => '0',
                    'min_lifetime' => '60',
                    'max_lifetime' => '2592000'
                )
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
            'date' => 'Wed, 15 Mar 2017 15:24:36 +0000',
        ),
);
