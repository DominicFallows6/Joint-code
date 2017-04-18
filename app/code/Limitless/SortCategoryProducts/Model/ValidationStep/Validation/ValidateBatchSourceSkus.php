<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ValidationStep\Validation;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

use Magento\Framework\Api\SearchCriteriaBuilder;

class ValidateBatchSourceSkus implements ValidateBatchSourceInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function getErrors(array $origBatchSource, array $validBatchSource): array
    {
        $errors = $this->getInvalidSkus(array_column($origBatchSource, self::SKU_COLUMN));
        return array_map(function ($invalidSku): array {
            return ['No product with SKU "%1" exists.', $invalidSku];
        }, $errors);
    }

    private function getInvalidSkus(array $skusToValidate): array
    {
        $validSkusMap = array_flip($this->filterInvalidSkus($skusToValidate));
        $invalidSkus = [];
        foreach ($skusToValidate as $idx => $sku) {
            if (! array_key_exists($sku, $validSkusMap)) {
                $invalidSkus[$idx] = $sku;
            }
        }
        
        return $invalidSkus;
    }

    private function filterInvalidSkus(array $potentiallyValidSkus)
    {
        $this->searchCriteriaBuilder->addFilter('sku', $potentiallyValidSkus, 'in');
        $searchResults = $this->productRepository->getList($this->searchCriteriaBuilder->create());
        $validSkus = array_map(function (ProductInterface $product) {
            return $product->getSku();
        }, (array) $searchResults->getItems());

        return $validSkus;
    }

}
