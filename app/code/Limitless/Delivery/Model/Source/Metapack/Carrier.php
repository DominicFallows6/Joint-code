<?php

namespace Limitless\Delivery\Model\Source\Metapack;

use Limitless\Delivery\Model\MetapackCarrierSorting;
use Limitless\Delivery\Model\MetapackCarrierSortingFactory;
use Magento\Framework\Option\ArrayInterface;

class Carrier implements ArrayInterface
{
    /**
     * @var MetapackCarrierSortingFactory
     */
    private $metapackCarrierSortingFactory;

    /**
     * Carrier constructor.
     * @param MetapackCarrierSortingFactory $metapackCarrierSortingFactory
     */
    public function __construct(MetapackCarrierSortingFactory $metapackCarrierSortingFactory) {
        $this->metapackCarrierSortingFactory = $metapackCarrierSortingFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        /** @var MetapackCarrierSorting $metapackCarrierSorting */
        $metapackCarrierSortValues = $this->metapackCarrierSortingFactory->create()->getCollection();

        $sortVals = [];
        foreach ($metapackCarrierSortValues as $sortVal) {
            $sortVals[] = ['value' => $sortVal['code'], 'label' => $sortVal['sort_ref_name']];
        }

        return $sortVals;
    }
}