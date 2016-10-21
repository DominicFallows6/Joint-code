<?php
return array(
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
                            'host' => '<mysql ip or host>',
                            'dbname' => '<database name>',
                            'username' => '<database user>',
                            'password' => '<database password>',
                            'active' => '1',
                            'persistent' => null,
                        ),
                    'default' =>
                        array(
                            'host' => '<mysql ip or host>',
                            'dbname' => '<database name>',
                            'username' => '<database user>',
                            'password' => '<database password>',
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
                                    'server' => '<redis ip or host>',
                                    'port' => '6379'
                                    //'database' => '1',
                                    //'compress_data' => '0'
                                ),
                        ),
                    'page_cache' =>
                        array(
                            'backend' => 'Cm_Cache_Backend_Redis',
                            'backend_options' =>
                                array(
                                    'server' => '<redis ip or host>',
                                    'port' => '6379',
                                    //'database' => '1',
                                    //'compress_data' => '0'
                                )
                        )
                )
        ),
    'crypt' =>
        array(
            'key' => '',
        ),
    'session' =>
        array (
            'save' => 'memcached',
            'save_path' => '<memcache ip or host>:<memcache port>'
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
    'cache_types' =>
        array(
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
    'install' =>
        array(
            'date' => '',
        ),
);
