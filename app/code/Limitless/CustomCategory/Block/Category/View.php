<?php

namespace Limitless\CustomCategory\Block\Category;

use Limitless\CustomCategory\Block\Html\CustomCategory;
use Magento\Catalog\Block\Category\View as ViewParent;
use Magento\Catalog\Helper\Category;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;

class View extends ViewParent
{
    /**
     * @var CustomCategory
     */
    private $customCategory;

    public function __construct(
        Context $context,
        Resolver $layerResolver,
        Registry $registry,
        Category $categoryHelper,
        CustomCategory $customCategory,
        array $data = []
    ){
        parent::__construct(
            $context,
            $layerResolver,
            $registry,
            $categoryHelper,
            $data);
        $this->customCategory = $customCategory;
    }

    /**
     * @return string
     */
    public function getCustomCategoryDescription()
    {
        if ($this->customCategory->hasCustomCategory() == true) {
            return $this->customCategory->getCustomDescription();
        } else {
            return '';
        }
    }

}