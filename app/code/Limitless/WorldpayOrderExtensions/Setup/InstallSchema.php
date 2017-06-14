<?php

namespace Limitless\WorldpayOrderExtensions\Setup;

use Limitless\WorldpayOrderExtensions\Model\WorldpayRiskScore;
use Limitless\WorldpayOrderExtensions\Model\ResourceModel\WorldpayRiskScore as WorldpayRiskScoreResource;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
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
        $table = $setup->getConnection()->newTable($setup->getTable(WorldpayRiskScoreResource::TABLE));
        $table->addColumn(WorldpayRiskScoreResource::ID_FIELD, Table::TYPE_INTEGER, null, [
            'primary' => true,
            'identity' => true,
            'unsigned' => true,
            'nullable' => false
        ]);
        $table->addColumn(WorldpayRiskScore::ORDER_ID, Table::TYPE_BIGINT, null, [
            'unsigned' => true,
            'nullable' => false,
        ]);
        $table->addColumn(WorldpayRiskScore::RISK_SCORE, Table::TYPE_INTEGER, null, [
            'nullable' => false,
        ]);
        $setup->getConnection()->createTable($table);
    }
}
