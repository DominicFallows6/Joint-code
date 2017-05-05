<?php

namespace Limitless\TagManagerDataLayer\Block\DataLayer;

use Limitless\TagManagerDataLayer\Api\DataLayerAbstract;
use Magento\Catalog\Api\ProductRepositoryInterface;
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

    /** @var ProductRepositoryInterface */
    private $productRepository;

    public function __construct(
        Context $context,
        DynamicRemarketing $dynamicRemarketingHelper,
        ProductRepositoryInterface $productRepository,
        $data = []
    ) {
        parent::__construct($context, $data);

        $this->dynamicRemarketingHelper = $dynamicRemarketingHelper;
        $this->dataLayerVariables = [];
        $this->productRepository = $productRepository;
    }

    public function initDataLayerVariables()
    {
        $this->loadProducts();
        $this->initDynamicRemarketingDLVariables();
    }

    /**
     * Assign products from product collection
     */
    private function loadProducts()
    {
        //See \Magento\Catalog\Block\Product\ListProduct _beforeToHtml()
        $productCollectionData = $this->buildSearchPageList();

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
        $this->searchProducts = $products;
    }

    /**
     * @return array
     */
    private function buildSearchPageList(): array
    {
        /* @see \Magento\Catalog\Block\Product\ListProduct _beforeToHtml()

        /** @var \Magento\Catalog\Block\Product\ListProduct\Interceptor $searchBlock */
        $searchBlock = $this->getLayout()->getBlock('search_result_list');

        /** @var \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar */
        $toolbar = $searchBlock->getToolbarBlock();
        $productCollection = $searchBlock->getLoadedProductCollection();

        $orders = $searchBlock->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }
        $sort = $searchBlock->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }
        $dir = $searchBlock->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }
        $modes = $searchBlock->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($productCollection);

        return $productCollection->getData();
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