<?php

namespace Limitless\CustomCategory\Repository;

use Limitless\CustomCategory\Model\CustomCategoryFactory;
use Limitless\CustomCategory\Model\ResourceModel\CustomCategory\Collection;
use Limitless\CustomCategory\Repository\CustomCategoryRepositoryInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class CustomCategoryRepository implements CustomCategoryRepositoryInterface
{
    /** @var $customCategoryFactory CustomCategoryFactory  */
    private $customCategoryFactory;

    /**
     * @param CustomCategoryFactory $customCategoryFactory
     */
    public function __construct(CustomCategoryFactory $customCategoryFactory)
    {
        $this->customCategoryFactory = $customCategoryFactory;
    }

    /**
     * @return Collection|AbstractCollection
     */
    public function getCustomCategoryCollection()
    {
        $customCategory = $this->customCategoryFactory->create();
        $collection = $customCategory->getCollection();
        return $collection;
    }

    /**
     * @param int $categoryId
     * @param int $status
     * @param int $storeId
     * @param string $attributes
     * @return \Limitless\CustomCategory\Model\CustomCategory|\Magento\Framework\DataObject
     */
    public function getCustomCategory($categoryId, $status, $storeId, $attributes)
    {
        $collection = $this->getCustomCategoryCollection();
        $collection->addFieldToFilter('category_id', $categoryId);
        $collection->addFieldToFilter('status', $status);
        $collection->addFieldToFilter('store_id', $storeId);
        $collection->addFieldToFilter('filter_attribute_ids', $attributes);
        return $collection->getFirstItem();
    }
    

}