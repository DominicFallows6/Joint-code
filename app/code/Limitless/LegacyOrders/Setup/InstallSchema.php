<?php

namespace Limitless\LegacyOrders\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Limitless\LegacyOrders\Model\ResourceModel\LegacyOrders as LegacyOrdersResource;
use Magento\Framework\DB\Ddl\Table;


class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $table = $setup->getConnection()->newTable($setup->getTable(LegacyOrdersResource::TABLE));

        $table->addColumn(LegacyOrdersResource::ID_FIELD, Table::TYPE_INTEGER, null, [
            'primary' => true,
            'identity' => true,
            'unsigned' => true,
            'nullable' => false
        ]);

        $table->addColumn(LegacyOrdersResource::LEGACY_ORDER_ID_FIELD, Table::TYPE_INTEGER, null, [
            'unsigned' => true,
            'nullable' => false
        ]);

        $table->addColumn(LegacyOrdersResource::EMAIL_FIELD, Table::TYPE_TEXT, 255, [
            'nullable' => false,
            'LENGTH' => 255
        ]);

        $table->addColumn('site_id', Table::TYPE_SMALLINT, null, [
            'nullable' => false,
            'unsigned' => true
        ]);

        $table->addColumn('currency_type', Table::TYPE_TEXT, 10, [
            'nullable' => false,
        ]);

        $table->addColumn('shipping_method', Table::TYPE_TEXT, 255, [
            'LENGTH' => 255,
        ]);

        $table->addColumn('order_phase', Table::TYPE_TEXT, 50, [
            'LENGTH' => 50,
        ]);

        $table->addColumn('order_date', Table::TYPE_DATETIME);
        $table->addColumn('order_total', Table::TYPE_TEXT, 50);
        $table->addColumn('order_subtotal', Table::TYPE_TEXT, 50);
        $table->addColumn('delivery_value', Table::TYPE_TEXT, 50);
        $table->addColumn('order_discount', Table::TYPE_TEXT, 50);
        $table->addColumn('payment_type', Table::TYPE_TEXT, 255);
        $table->addColumn('order_items', Table::TYPE_TEXT);
        $table->addColumn('delivery_address', Table::TYPE_TEXT);
        $table->addColumn('billing_address', Table::TYPE_TEXT);
        $table->addColumn('vat_value', Table::TYPE_TEXT, 50);
        
        $table->addIndex(LegacyOrdersResource::EMAIL_FIELD, [LegacyOrdersResource::EMAIL_FIELD]);
        $table->addIndex(LegacyOrdersResource::LEGACY_ORDER_ID_FIELD, [LegacyOrdersResource::LEGACY_ORDER_ID_FIELD]);
        
        $setup->getConnection()->createTable($table);

    }

}
