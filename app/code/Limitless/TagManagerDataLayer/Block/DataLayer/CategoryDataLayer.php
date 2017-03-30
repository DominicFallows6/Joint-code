<?php

namespace Limitless\TagManagerDataLayer\Block\DataLayer;

use Limitless\TagManagerDataLayer\Api\DataLayerAbstract;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Registry;
use Magento\Framework\App\Request\Http;
use Limitless\TagManagerDataLayer\Helper\TagsDataLayer\DynamicRemarketing;
use Magento\Framework\View\Element\Template\Context;

class CategoryDataLayer extends DataLayerAbstract
{
    /** @var array */
    private $dataLayerVariables;

    /** @var Category */
    private $category;

    /** @var Http */
    private $httpRequest;

    /** @var DynamicRemarketing */
    private $dynamicRemarketingHelper;

    /** @var \Magento\Catalog\Model\Product[] */
    private $products;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var SearchCriteriaInterface */
    private $searchCriteria;

    /** @var FilterGroup */
    private $filterGroup;

    /** @var FilterBuilder */
    private $filterBuilder;

    /** @var SortOrder */
    private $sortOrderBuilder;

    public function __construct(
        Context $context,
        Registry $registry,
        DynamicRemarketing $dynamicRemarketingHelper,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaInterface $searchCriteria,
        FilterGroup $filterGroup,
        FilterBuilder $filterBuilder,
        SortOrderBuilder $sortOrderBuilder,
        Http $http,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->category = $registry->registry('current_category');
        $this->httpRequest = $http;
        $this->dynamicRemarketingHelper = $dynamicRemarketingHelper;
        $this->productRepository = $productRepository;
        $this->searchCriteria = $searchCriteria;
        $this->filterGroup = $filterGroup;
        $this->filterBuilder = $filterBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->dataLayerVariables = [];
    }

    public function initDataLayerVariables()
    {
        $this->loadProductCollection();
        $this->initDynamicRemarketingDLVariables();
    }

    /**
     * @return array
     */
    public function getDataLayerVariables(): array
    {
        return $this->dataLayerVariables;
    }

    private function loadProductCollection()
    {
        $this->products = $this->getProductListWithFilters(
            $this->category->getId(),
            $this->getSortBy(),
            $this->getSortDirection()
        )->getItems();
    }

    /**
     * @param integer $categoryId
     * @param string $sortBy
     * @param string $sortDirection
     * @return SearchResultsInterface
     */
    private function getProductListWithFilters($categoryId, $sortBy, $sortDirection): SearchResultsInterface
    {
        $this->applyFiltersToSearchCriteria($categoryId);
        $this->applySortOrderAndSizeToSearchCriteria($sortBy, $sortDirection);

        return $this->productRepository->getList($this->searchCriteria);
    }

    private function applyFiltersToSearchCriteria($categoryId)
    {
        $filters = $this->getFiltersArray($categoryId);

        $filterGroup = $this->filterGroup->setFilters($filters);
        $filterGroupArray = [$filterGroup];
        $this->searchCriteria->setFilterGroups($filterGroupArray);
    }

    /**
     * @param $categoryId
     * @return \Magento\Framework\Api\Filter[]
     */
    private function getFiltersArray($categoryId): array
    {
        //TODO add filters (e.g ?price=50-)
        $filterCategory = $this->filterBuilder->setField('category_id')->setValue($categoryId)->create();
        $filterVisibility = $this->filterBuilder->setField('visibility')
            ->setValue(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)->create();
        $filterStatus = $this->filterBuilder->setField('status')
            ->setValue(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)->create();

        return [$filterCategory, $filterVisibility, $filterStatus];
    }

    private function applySortOrderAndSizeToSearchCriteria($sortBy, $sortDirection)
    {
        $sortingOrderAndDirection = $this->sortOrderBuilder->setField($sortBy)->setDirection($sortDirection)->create();
        $this->searchCriteria->setSortOrders([$sortingOrderAndDirection]);

        $maxProductsToShow = $this->dynamicRemarketingHelper->getMaxProductsDisplayedPublic();
        $this->searchCriteria->setPageSize($maxProductsToShow)->setCurrentPage(1);
    }

    /**
     * @return string
     */
    private function getSortBy(): string
    {
        $allowedSortBy = $this->category->getAvailableSortByOptions();
        $sortBy = $this->httpRequest->get('product_list_order') ?? 'position';
        if (!$allowedSortBy[$sortBy]) {
            $sortBy = $this->category->getDefaultSortBy();
        }
        return $sortBy;
    }

    /**
     * @return string
     */
    private function getSortDirection(): string
    {
        $allowedOrderBy = [SortOrder::SORT_ASC => 0, SortOrder::SORT_DESC => 1];
        $orderBy = strtoupper($this->httpRequest->get('product_list_dir')) ?? 'ASC';
        if (!isset($allowedOrderBy[$orderBy])) {
            $orderBy = 'ASC';
        }
        return $orderBy;
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