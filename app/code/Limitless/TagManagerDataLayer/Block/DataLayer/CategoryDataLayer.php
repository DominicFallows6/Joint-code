<?php

namespace Limitless\TagManagerDataLayer\Block\DataLayer;

use Limitless\TagManagerDataLayer\Api\DataLayerAbstract;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\Registry;
use Limitless\TagManagerDataLayer\Helper\TagsDataLayer\DynamicRemarketing;
use Magento\Framework\View\Element\Template\Context;

class CategoryDataLayer extends DataLayerAbstract
{
    /** @var array */
    private $dataLayerVariables;

    /** @var Category */
    private $category;

    /** @var DynamicRemarketing */
    private $dynamicRemarketingHelper;

    /** @var \Magento\Catalog\Model\Product[] */
    private $products;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    public function __construct(
        Context $context,
        Registry $registry,
        DynamicRemarketing $dynamicRemarketingHelper,
        ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->category = $registry->registry('current_category');
        $this->dynamicRemarketingHelper = $dynamicRemarketingHelper;
        $this->productRepository = $productRepository;
        $this->dataLayerVariables = [];
    }

    public function initDataLayerVariables()
    {
        $this->loadProducts();
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
     * Assign products from product collection
     */
    private function loadProducts()
    {
        //See \Magento\Catalog\Block\Product\ListProduct _beforeToHtml()
        $productCollectionData = $this->buildCategoryProductList();

        $maxProductsToShow = $this->dynamicRemarketingHelper->getMaxProductsDisplayedPublic();
        $counter = 0;
        $products = [];

        while($counter < $maxProductsToShow) {

            if (isset($productCollectionData[$counter])) {
                $productData = $productCollectionData[$counter];

                $products[] = $this->productRepository->get($productData['sku']);
                $counter++;
            }
            else {
                break;
            }
        }
        $this->products = $products;
    }

    /**
     * @return array
     */
    private function buildCategoryProductList(): array
    {
        /* @see \Magento\Catalog\Block\Product\ListProduct _beforeToHtml()

        /** @var \Magento\Catalog\Block\Product\ListProduct\Interceptor $categoryBlock */
        $categoryBlock = $this->getLayout()->getBlock('category.products.list');

        /** @var \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar */
        $toolbar = $categoryBlock->getToolbarBlock();
        $productCollection = $categoryBlock->getLoadedProductCollection();

        $orders = $categoryBlock->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }
        $sort = $categoryBlock->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }
        $dir = $categoryBlock->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }
        $modes = $categoryBlock->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($productCollection);

        return $productCollection->getData();
    }

    private function initDynamicRemarketingDLVariables()
    {
        $this->dynamicRemarketingHelper->buildAllDynamicRemarketingValues(
            'category',
            $this->products,
            $this->escapeHtml($this->category->getName())
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

}