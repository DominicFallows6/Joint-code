<?php

namespace Limitless\TagManagerDataLayer\Block\DataLayer;

use Limitless\TagManagerDataLayer\Api\DataLayerAbstract;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Framework\View\Element\Template\Context;
use Limitless\TagManagerDataLayer\Helper\TagsDataLayer\DynamicRemarketing;

class SearchDataLayer extends DataLayerAbstract
{
    /** @var array */
    private $dataLayerVariables;

    /** @var DynamicRemarketing */
    private $dynamicRemarketingHelper;

    /** @var \Magento\Catalog\Model\Product[]  */
    private $searchProducts = [];

    /** @var ListProduct */
    private $listProduct;

    public function __construct(
        Context $context,
        DynamicRemarketing $dynamicRemarketingHelper,
        ListProduct $listProduct,
        $data = []
    ) {
        parent::__construct($context, $data);

        $this->dynamicRemarketingHelper = $dynamicRemarketingHelper;
        $this->listProduct = $listProduct;
        $this->dataLayerVariables = [];
    }

    public function initDataLayerVariables()
    {
        $this->searchProducts = $this->listProduct->getLoadedProductCollection()->getItems();
        $this->initDynamicRemarketingDLVariables();
    }

    /**
     * @return array
     */
    public function getDataLayerVariables(): array
    {
        return $this->dataLayerVariables;
    }

    private function initDynamicRemarketingDLVariables()
    {
        $this->dynamicRemarketingHelper->buildAllDynamicRemarketingValues('searchresults', $this->searchProducts);

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