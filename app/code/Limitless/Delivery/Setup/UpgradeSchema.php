<?php

namespace Limitless\Delivery\Setup;

use Limitless\Delivery\Model\AllocationFilter as AllocationFilterModel;
use Limitless\Delivery\Model\ResourceModel\AllocationFilter;
use Limitless\Delivery\Model\MetapackCarrierSorting;
use Limitless\Delivery\Model\ResourceModel\MetapackCarrierSorting as MetapackCarrierSortingResource;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (!$setup->getConnection()->tableColumnExists(AllocationFilter::TABLE, AllocationFilterModel::SHIPPING_METHOD)) {
            $setup->getConnection()->addColumn(AllocationFilter::TABLE, AllocationFilterModel::SHIPPING_METHOD, [
                'type' => Table::TYPE_TEXT,
                'nullable' => false,
                'length' => 2048,
                'comment' => 'Allocation filter string which can be used as Magento shipping method code'
            ]);
            $setup->getConnection()->modifyColumn(AllocationFilter::TABLE, AllocationFilterModel::ALLOCATION_FILTER, [
                'type' => Table::TYPE_TEXT,
                'nullable' => false,
                'length' => 2048,
                'comment' => 'String for Solvitt integration (duplicate of shipping_method without carrier prefix)'
            ]);
        }

        $table = $setup->getConnection()->newTable($setup->getTable(MetapackCarrierSortingResource::TABLE));
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
        $setup->getConnection()->createTable($table);
    }
}