<?php

namespace Limitless\ProductOOSSortOrder\Model\ResourceModel;

use Magento\Catalog\Model\ResourceModel\Category as CategoryResource;

class CategoryProducts
{
    /** @var CategoryResource */
    private $categoryResource;

    public function __construct(CategoryResource $categoryResource)
    {
        $this->categoryResource = $categoryResource;
    }

    /**
     * @param int[] $productIds
     * @return int[]
     */
    public function getCategoryIdsForProductIds(array $productIds)
    {
        $select = $this->categoryResource->getConnection()->select();
        $select->from($this->categoryResource->getCategoryProductTable(), 'category_id')
            ->where('product_id IN (?)', $productIds);

        return $this->categoryResource->getConnection()->fetchCol($select);
    }
}