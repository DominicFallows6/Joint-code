<?php

namespace Limitless\TagManagerDataLayer\Block\DataLayer;

use Limitless\TagManagerDataLayer\Api\DataLayerAbstract;
use Magento\Catalog\Model\Category;
use Magento\Framework\View\Element\Template\Context;
use Limitless\TagManagerDataLayer\Helper\TagsDataLayer\DynamicRemarketing;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\CategoryFactory;

class ProductDataLayer extends DataLayerAbstract
{
    /** @var array */
    private $dataLayerVariables;

    /** @var Product */
    private $product;

    /** @var CategoryFactory */
    private $categoryFactory;

    /** @var DynamicRemarketing */
    private $dynamicRemarketingHelper;

    /** @var Category */
    private $category;

    public function __construct(
        Context $context,
        DynamicRemarketing $dynamicRemarketingHelper,
        Registry $registry,
        CategoryFactory $categoryFactory,
        $data = []
    ) {
        parent::__construct($context, $data);

        $this->product = $registry->registry('current_product');
        $this->category = $registry->registry('current_category');
        $this->categoryFactory = $categoryFactory;
        $this->dynamicRemarketingHelper = $dynamicRemarketingHelper;
        $this->dataLayerVariables = [];
    }

    public function initDataLayerVariables()
    {
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
        //Passing -1 will set total to ecommValue (without square brackets)
        $this->dynamicRemarketingHelper->buildAllDynamicRemarketingValues(
            'product',
            [$this->product],
            $this->getEcommProdCategory(),
            -1
        );

        $this->mergeIntoDataLayer($this->dynamicRemarketingHelper->getAllDynamicRemarketingValuesInArray());
    }

    /**
     * @param array $mergeRequest
     */
    private function mergeIntoDataLayer($mergeRequest)
    {
        $this->dataLayerVariables = array_merge($mergeRequest, $this->dataLayerVariables);
    }

    private function getEcommProdCategory()
    {
        //Todo:
        //Products in multiple categories have the same product URL
        //As product pages will be cached we will use default category for now
        //may be a way to hole punch data in (see breadcrumbs)
        //$categoryId = $this->product->getCategoryId();

        $categoryIds = $this->product->getCategoryIds();

        if ($this->category) {
            return $this->escapeHtml($this->category->getName());
        } else if ($categoryIds[0]) {
            $category = $this->categoryFactory->create();
            $category->getResource()->load($category, $categoryIds[0], 'category_id');
            return $this->escapeHtml($category->getName());
        }
        return '';
    }
}