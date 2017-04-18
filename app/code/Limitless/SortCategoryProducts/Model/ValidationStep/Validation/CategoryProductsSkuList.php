<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ValidationStep\Validation;

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\Data\CategoryProductLinkInterface;

class CategoryProductsSkuList
{
    /**
     * @var CategoryLinkManagementInterface
     */
    private $categoryLinkManagement;

    public function __construct(CategoryLinkManagementInterface $categoryLinkManagement)
    {
        $this->categoryLinkManagement = $categoryLinkManagement;
    }

    public function getCategoryProductSkus($categoryId): array
    {
        $categoryProducts = $this->categoryLinkManagement->getAssignedProducts($categoryId);

        return array_map(function (CategoryProductLinkInterface $categoryProduct) {
            return $categoryProduct->getSku();
        }, (array) $categoryProducts);
    }
}
