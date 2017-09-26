<?php

namespace Limitless\CustomCategory\Setup;

use Limitless\CustomCategory\Model\CustomCategory as CustomCategoryModel;
use Limitless\CustomCategory\Model\ResourceModel\CustomCategory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements  UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '0.0.28') < 0) {
            if (!$setup->getConnection()->tableColumnExists(CustomCategory::TABLE, CustomCategoryModel::META_DESCRIPTION)) {
                $setup->getConnection()->addColumn(CustomCategory::TABLE, CustomCategoryModel::META_DESCRIPTION, [
                    'type' => Table::TYPE_TEXT,
                    'comment' => 'Adding column for meta description'
                ]);
            }
        }
        $setup->endSetup();
    }
}