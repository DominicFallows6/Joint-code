<?php

namespace Limitless\Delivery\Model\Source\Metapack;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Limitless\Delivery\Model\ResourceModel\MetapackCarrierSorting as MetapackCarrierSortingResource;

class Carrier implements ArrayInterface
{
    /**
     * @var ModuleDataSetupInterface setup
     */
    private $setup;

    /**
     * Carrier constructor.
     * @param ModuleDataSetupInterface $setup
     */
    public function __construct(ModuleDataSetupInterface $setup)
    {
        $this->setup = $setup->startSetup();
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $sortVals = [];
        $select = $this->setup->getConnection()->select()->from(
            ['cso' => $this->setup->getTable(MetapackCarrierSortingResource::TABLE)]
        );
        $metapackCarrierSortValues = $this->setup->getConnection()->fetchAll($select);

        foreach ($metapackCarrierSortValues as $sortVal) {
            $sortVals[] = ['value' => $sortVal['code'], 'label' => $sortVal['sort_ref_name']];
        }

        return $sortVals;
    }
}