<?php
/**
 * Created by PhpStorm.
 * User: tprocter
 * Date: 01/03/2017
 * Time: 13:59
 */

namespace Limitless\SubCategoryList\Model;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Registry;

class SubCategories
{
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var CollectionFactory
     */
    private $categoryCollectionFactory;

    public function __construct(Registry $registry, CollectionFactory $categoryCollectionFactory)
    {

        $this->registry = $registry;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * @return Category
     */
    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }

    /**
     * @return \Magento\Catalog\Model\Category[]
     */
    public function getCurrentCategoryChildren()
    {
        $category = $this->getCurrentCategory();
        $collection = $this->categoryCollectionFactory->create();
        $collection
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('url')
            ->addAttributeToSelect('level')
            ->addAttributeToFilter('is_active', 1)
            ->addIdFilter($category->getChildren())
            ->setOrder('position', \Magento\Framework\DB\Select::SQL_ASC)
            ->joinUrlRewrite();

        return $collection->getItems();
    }

    public function getCurrentCategoryLevel()
    {
        return $this->getCurrentCategory()->getLevel();
    }

}