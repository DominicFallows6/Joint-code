<?php

namespace Limitless\TagManagerDataLayer\Block\DataLayer;

use Limitless\TagManagerDataLayer\Api\DataLayerAbstract;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use Magento\CatalogSearch\Model\Advanced as AdvancedSearch;
use Limitless\TagManagerDataLayer\Helper\TagsDataLayer\DynamicRemarketing;

class AdvanceSearchDataLayer extends DataLayerAbstract
{
    /** @var array */
    private $dataLayerVariables;

    /** @var DynamicRemarketing */
    private $dynamicRemarketingHelper;

    /** @var \Magento\Catalog\Model\Product[] */
    private $productItems = [];

    /** @var Registry */
    private $registry;

    /** @var AdvancedSearch */
    private $advancedSearch;

    public function __construct(
        Context $context,
        Registry $registry,
        AdvancedSearch $advancedSearch,
        DynamicRemarketing $dynamicRemarketingHelper,
        $data = []
    ) {
        parent::__construct($context, $data);

        $this->dynamicRemarketingHelper = $dynamicRemarketingHelper;
        $this->registry = $registry;
        $this->advancedSearch = $advancedSearch;
        $this->dataLayerVariables = [];
    }

    public function initDataLayerVariables()
    {
        if ($this->registry->registry('advanced_search_conditions')) {
            $this->loadAdvanceSearchProductItems($this->advancedSearch);
        }

        $this->initDynamicRemarketingDLVariables();
    }

    /**
     * @return array
     */
    public function getDataLayerVariables(): array
    {
        return $this->dataLayerVariables;
    }

    /**
     * @param AdvancedSearch $advancedSearch
     */
    private function loadAdvanceSearchProductItems(AdvancedSearch $advancedSearch)
    {
        /** @var \Magento\CatalogSearch\Model\ResourceModel\Advanced\Collection $itemCollection */
        $itemCollection = $advancedSearch->getProductCollection();
        $this->productItems = $itemCollection->getItems();
    }

    private function initDynamicRemarketingDLVariables()
    {
        $this->dynamicRemarketingHelper->buildAllDynamicRemarketingValues('searchresults', $this->productItems);

        $this->mergeIntoDataLayer($this->dynamicRemarketingHelper->getAllDynamicRemarketingValuesInArray());
    }

    /**
     * @param array $mergeRequest
     */
    private function mergeIntoDataLayer($mergeRequest)
    {
        $this->dataLayerVariables = array_merge($mergeRequest, $this->dataLayerVariables);
    }
}