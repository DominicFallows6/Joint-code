<?php

namespace Limitless\Delivery\Setup;

use Limitless\Delivery\Model\AllocationFilter;
use Limitless\Delivery\Model\ResourceModel\AllocationFilter as AllocationFilterResource;
use Limitless\Delivery\Model\MetapackCarrierSorting;
use Limitless\Delivery\Model\ResourceModel\MetapackCarrierSorting as MetapackCarrierSortingResource;
use Magento\Framework\DB\Ddl\Table;
use	Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /** @var SchemaSetupInterface */
    private $setup;

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->setup = $setup;
        $this->setupAllocationFilter();
        $this->setupMetapackCarrierSorting();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    private function setupAllocationFilter()
    {
        $table = $this->setup->getConnection()->newTable($this->setup->getTable(AllocationFilterResource::TABLE));
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
        $this->setup->getConnection()->createTable($table);
    }

    private function setupMetapackCarrierSorting()
    {
        $table = $this->setup->getConnection()->newTable($this->setup->getTable(MetapackCarrierSortingResource::TABLE));
        $table->addColumn(MetapackCarrierSortingResource::ID_FIELD, Table::TYPE_INTEGER, null, [
            'primary' => true,
            'identity' => true,
            'unsigned' => true,
            'nullable' => false
        ]);
        $table->addColumn(MetapackCarrierSorting::CODE, Table::TYPE_INTEGER, null, [
            'unsigned' => true,
            'nullable' => false
        ]);
        $table->addColumn(MetapackCarrierSorting::SORT_REF_NAME, Table::TYPE_TEXT, 64, [
            'nullable' => false,
        ]);
        $this->setup->getConnection()->createTable($table);
    }
}