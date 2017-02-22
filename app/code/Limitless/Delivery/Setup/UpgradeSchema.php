<?php

namespace Limitless\Delivery\Setup;

use Limitless\Delivery\Model\AllocationFilter as AllocationFilterModel;
use Limitless\Delivery\Model\ResourceModel\AllocationFilter;
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
    }
}