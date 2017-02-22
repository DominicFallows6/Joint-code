<?php

namespace Limitless\Delivery\Setup;

use Limitless\Delivery\Model\AllocationFilter;
use Limitless\Delivery\Model\ResourceModel\AllocationFilter as AllocationFilterResource;
use Magento\Framework\DB\Ddl\Table;
use	Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->setupAllocationFilter($setup);
    }


    /**
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    private function setupAllocationFilter(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable($setup->getTable(AllocationFilterResource::TABLE));
        $table->addColumn(AllocationFilterResource::ID_FIELD, Table::TYPE_INTEGER, null, [
            'primary' => true,
            'identity' => true,
            'unsigned' => true,
            'nullable' => false
        ]);
        $table->addColumn(AllocationFilter::QUOTE_ID, Table::TYPE_INTEGER, null, [
            'unsigned' => true,
            'nullable' => true
        ]);
        $table->addColumn(AllocationFilter::ORDER_ID, Table::TYPE_INTEGER, null, [
            'unsigned' => true,
            'nullable' => true
        ]);
        $table->addColumn(AllocationFilter::ALLOCATION_FILTER, Table::TYPE_TEXT, 256, [
            'nullable' => false,
        ]);
        $setup->getConnection()->createTable($table);
    }
}