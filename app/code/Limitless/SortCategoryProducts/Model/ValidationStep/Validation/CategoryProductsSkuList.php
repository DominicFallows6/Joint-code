<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ValidationStep\Validation;

use Magento\Catalog\Model\ResourceModel\Product as ProductResource;

class CategoryProductsSkuList
{
    /**
     * @var array[]
     */
    private $memoizedCategorySkuMap = [];

    /**
     * @var ProductResource
     */
    private $productResource;

    public function __construct(
        ProductResource $productResource
    ) {
        $this->productResource = $productResource;
    }

    /**
     * @param int $categoryId
     * @return string[]
     */
    public function getCategoryProductSkus($categoryId): array
    {
        if (! isset($this->memoizedCategorySkuMap[$categoryId])) {
            $this->memoizedCategorySkuMap[$categoryId] = $this->loadSkusAssignedToCategory($categoryId);
        }

        return $this->memoizedCategorySkuMap[$categoryId];
    }

    private function loadSkusAssignedToCategory($categoryId): array
    {
        $productIds = $this->getCategoryProductIds($categoryId);

        return array_map(function (array $row) {
            return $row['sku'];
        }, $this->productResource->getProductsSku($productIds));
    }

    /**
     * @param int $categoryId
     * @return int[]
     */
    private function getCategoryProductIds($categoryId): array
    {
        $select = $this->productResource->getConnection()->select()
            ->from($this->productResource->getTable('catalog_category_product'), ['product_id'])
            ->where('category_id=?', $categoryId);

        return $this->productResource->getConnection()->fetchCol($select);
    }
}
