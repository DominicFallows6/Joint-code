<?php
namespace Limitless\CustomCategory\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Limitless\CustomCategory\Model\ResourceModel\CustomCategory as CustomCategoryResource;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        
        $tableName = $installer->getTable(CustomCategoryResource::TABLE);

        if ($installer->getConnection()->isTableExists($tableName) != true) {

            $table = $installer->getConnection()->newTable($setup->getTable(CustomCategoryResource::TABLE));

            $table->addColumn(CustomCategoryResource::ID_FIELD, Table::TYPE_INTEGER, null, [
                'primary' => true,
                'identity' => true,
                'unsigned' => true,
                'nullable' => false
            ]);

            $table->addColumn(CustomCategoryResource::CATEGORY_ID_FIELD, Table::TYPE_INTEGER, null, [
                'unsigned' => true,
                'nullable' => false
            ]);

            $table->addColumn('category_description', Table::TYPE_TEXT);
            $table->addColumn('category_heading', Table::TYPE_TEXT);
            $table->addColumn('filter_attribute_ids', Table::TYPE_TEXT);
            $table->addColumn('static_block', Table::TYPE_TEXT);
            $table->addColumn('status', Table::TYPE_INTEGER);
            $table->addColumn('store_id', Table::TYPE_INTEGER);
            $table->addColumn('meta_description', Table::TYPE_TEXT);

            $indexColumns = [
                CustomCategoryResource::CATEGORY_ID_FIELD,
                CustomCategoryResource::STORE_ID,
                CustomCategoryResource::STATUS
            ];
            $table->addIndex($setup->getIdxName(CustomCategoryResource::TABLE, $indexColumns), $indexColumns);

            $installer->getConnection()->createTable($table);

        }
        $installer->endSetup();
    }

}