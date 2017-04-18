<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ValidationStep\Validation;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

class CategoryIdList
{
    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    public function __construct(CategoryCollectionFactory $categoryCollectionFactory)
    {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    public function getAllCategoryIds()
    {
        return $this->categoryCollectionFactory->create()->getAllIds();
    }
}
