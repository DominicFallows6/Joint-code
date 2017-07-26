<?php

namespace Limitless\CustomCategory\Repository;

interface CustomCategoryRepositoryInterface
{
    /**
     * @return \Limitless\CustomCategory\Model\ResourceModel\CustomCategory\Collection
     */
    public function getCustomCategoryCollection();

    /**
     * @param int $categoryId
     * @param int $status
     * @param int $storeId
     * @param string $attributes
     * @return \Limitless\CustomCategory\Model\CustomCategory
     */
    public function getCustomCategory($categoryId, $status, $storeId, $attributes);
}