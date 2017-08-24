<?php

namespace Limitless\Delivery\Setup;

use Limitless\Delivery\Model\ResourceModel\MetapackCarrierSorting as MetapackCarrierSortingResource;
use Limitless\Delivery\Model\MetapackCarrierSorting;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    private $attributeRepository;

    /**
     * @var ModuleDataSetupInterface setup
     */
    private $setup;

    /**
     * UpgradeData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        AttributeRepositoryInterface $attributeRepository
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->setup = $setup->startSetup();
        $this->addAttributes($this->setup);
        if (version_compare($context->getVersion(), '0.0.26', '<')) {
            $this->insertMetapackCarrierSorting();
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function addAttributes(ModuleDataSetupInterface $setup)
    {
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY,
            'length',
            [
                'type' => 'int',
                'input' => 'text',
                'label' => 'Length',
                'required' => false,
                'user_defined' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Shipping',
            ]
        );

        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY,
            'width',
            [
                'type' => 'int',
                'input' => 'text',
                'label' => 'Width',
                'required' => false,
                'user_defined' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Shipping',
            ]
        );

        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY,
            'height',
            [
                'type' => 'int',
                'input' => 'text',
                'label' => 'Height',
                'required' => false,
                'user_defined' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Shipping',
            ]
        );

        try {
            $attribute = $this->attributeRepository->get(\Magento\Catalog\Model\Product::ENTITY, 'carrier_option');
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $attribute = false;
            echo $e->getMessage().PHP_EOL;
        }

        if (!$attribute) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'carrier_option',
                [
                    'type' => 'text',
                    'label' => 'Carrier Option',
                    'input' => 'multiselect',
                    'required' => false,
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'visible' => true,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'filterable_in_search' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'unique' => true,
                    'group' => 'Shipping',
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                    'option' => [
                        'values' => [
                            'FRAGILE',
                            'HIVALUE',
                            'BULKY',
                            'HAZARDOUS',
                            'PALLET'
                        ]
                    ]
                ]
            );
        }
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

            $select = $this->setup->getConnection()->select()->from(
                ['cso' => $this->setup->getTable(MetapackCarrierSortingResource::TABLE)]
            );
            $select->where('cso.'.MetapackCarrierSorting::SORT_REF_NAME.' = "'.$metapackCarrierSortingOption.'"');
            $result = $this->setup->getConnection()->fetchAll($select);

            if (!isset($result[0][MetapackCarrierSorting::SORT_REF_NAME])) {
                $this->setup->getConnection()->insert(MetapackCarrierSortingResource::TABLE, [
                    MetapackCarrierSorting::CODE => $key,
                    MetapackCarrierSorting::SORT_REF_NAME => $metapackCarrierSortingOption
                ]);
            }
        }
    }
}