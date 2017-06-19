<?php

namespace Limitless\DeliveryPriority\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Limitless\DeliveryPriority\Model\ResourceModel\Priority as PriorityResource;
use Limitless\DeliveryPriority\Model\Priority;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->setupDeliveryPriority($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    private function setupDeliveryPriority(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable($setup->getTable(PriorityResource::TABLE));
        $table->addColumn(PriorityResource::ID_FIELD, Table::TYPE_INTEGER, null, [
            'primary' => true,
            'identity' => true,
            'unsigned' => true,
            'nullable' => false
        ]);
        $table->addColumn(Priority::ORDER_ID, Table::TYPE_INTEGER, null, [
            'unsigned' => true,
            'nullable' => false
        ]);
        $table->addColumn(Priority::DELIVERY_PRIORITY, Table::TYPE_TEXT, 255, [
            'nullable' => false,
        ]);
        $setup->getConnection()->createTable($table);
    }
}