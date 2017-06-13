<?php

namespace Limitless\Delivery\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Limitless\Delivery\Model\MetapackCarrierSorting;
use Limitless\Delivery\Model\ResourceModel\MetapackCarrierSorting as MetapackCarrierSortingResource;

class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var ModuleDataSetupInterface setup
     */
    private $setup;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->setup = $setup->startSetup();
        $this->insertPalletAttribute();
        $this->insertMetapackCarrierSorting();
    }

    private function insertPalletAttribute()
    {
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create();

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'pallet',
            [
                'type' => 'int',
                'input' => 'boolean',
                'label' => 'Pallet',
                'required' => false,
                'user_defined' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Shipping',
            ]
        );
    }

    private function insertMetapackCarrierSorting()
    {
        $this->setup->startSetup();

        $metapackCarrierSortingOptions = [
            0 => 'SORT_NONE',
            1 => 'SORT_CARRIER_SERVICE_TYPE',
            2 => 'SORT_EARLIEST_COLLECTION_ASCENDING',
            3 => 'SORT_LATEST_COLLECTION_DESCENDING',
            4 => 'SORT_EARLIEST_DELIVERY_ASCENDING',
            5 => 'SORT_LATEST_DELIVERY_DESCENDING',
            6 => 'SORT_COST_CHEAPEST',
            7 => 'SORT_CARRIER',
            8 => 'SORT_EARLIEST_COLLECTION_DAY_ASCENDING',
            9 => 'SORT_EARLIEST_COLLECTION_DAY_DESCENDING',
            10 => 'SORT_EARLIEST_DELIVERY_DAY_ASCENDING',
            11 => 'SORT_EARLIEST_DELIVERY_DAY_DESCENDING',
            12 => 'SORT_SERVICE',
            13 => 'SORT_LOWEST_SCORE',
            14 => 'SORT_HIGHEST_SCORE',
            15 => 'SORT_EARLIEST_CUT_OFF',
            16 => 'SORT_LATEST_CUT_OFF',
            17 => 'SORT_SERVICE_GROUP',
            18 => 'SORT_EARLIEST_COLLECTION_DESCENDING',
            19 => 'SORT_LATEST_COLLECTION_ASCENDING',
            20 => 'SORT_EARLIEST_DELIVERY_DESCENDING',
            21 => 'SORT_LATEST_DELIVERY_ASCENDING',
            22 => 'SORT_LATEST_COLLECTION_DAY_ASCENDING	',
            23 => 'SORT_LATEST_COLLECTION_DAY_DESCENDING',
            24 => 'SORT_LATEST_DELIVERY_DAY_ASCENDING',
            25 => 'SORT_LATEST_DELIVERY_DAY_DESCENDING',
            26 => 'SORT_EARLIEST_COLLECTION_TIME_ASCENDING',
            27 => 'SORT_EARLIEST_COLLECTION_TIME_DESCENDING',
            28 => 'SORT_EARLIEST_DELIVERY_TIME_ASCENDING',
            29 => 'SORT_EARLIEST_DELIVERY_TIME_DESCENDING',
            30 => 'SORT_LATEST_COLLECTION_TIME_ASCENDING',
            31 => 'SORT_LATEST_COLLECTION_TIME_DESCENDING',
            32 => 'SORT_LATEST_DELIVERY_TIME_ASCENDING',
            33 => 'SORT_LATEST_DELIVERY_TIME_DESCENDING',
            34 => 'SORT_TRANSIT_TIME_ASCENDING',
            35 => 'SORT_TRANSIT_TIME_DESCENDING'
        ];

        foreach ($metapackCarrierSortingOptions as $key => $metapackCarrierSortingOption) {
            $this->setup->getConnection()->insert(MetapackCarrierSortingResource::TABLE, [
                MetapackCarrierSorting::CODE => $key,
                MetapackCarrierSorting::SORT_REF_NAME => $metapackCarrierSortingOption
            ]);
        }
    }
}