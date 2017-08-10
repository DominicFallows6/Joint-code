<?php

namespace Limitless\TrustpilotApi\Setup;

use Limitless\TrustpilotApi\Model\TrustpilotCache;
use Limitless\TrustpilotApi\Model\ResourceModel\TrustpilotCache as TrustpilotCacheResource;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->setupTrustpilotApi($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    private function setupTrustpilotApi(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable($setup->getTable(TrustpilotCacheResource::TABLE));
        $table->addColumn(TrustpilotCacheResource::ID_FIELD, Table::TYPE_INTEGER, null, [
            'primary' => true,
            'identity' => true,
            'unsigned' => true,
            'nullable' => false
        ]);
        $table->addColumn(TrustpilotCache::STORE_CODE, Table::TYPE_INTEGER, null, [
            'unsigned' => true,
            'unique' => true,
            'nullable' => false,
        ]);
        $table->addColumn(TrustpilotCache::BUSINESS_UNITS_CACHE, Table::TYPE_TEXT, null, [
            'unsigned' => true,
            'nullable' => true,
            'length' => 0,
        ]);
        $table->addColumn(TrustpilotCache::REVIEW_CACHE, Table::TYPE_TEXT, null, [
            'unsigned' => true,
            'nullable' => true,
            'length' => 0,
        ]);
        $table->addColumn(TrustpilotCache::DATE_CACHE_UPDATED, Table::TYPE_TIMESTAMP, null, [
            'nullable' => true, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_UPDATE,
        ]);
        $setup->getConnection()->createTable($table);
    }
}
