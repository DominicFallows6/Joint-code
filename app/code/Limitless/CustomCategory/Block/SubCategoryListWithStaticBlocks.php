<?php

namespace Limitless\CustomCategory\Block;

use Limitless\CustomCategory\Block\Html\CustomCategory;
use Limitless\SubCategoryList\Block\View;
use Limitless\SubCategoryList\Model\SubCategories;
use Magento\Framework\View\Element\Template\Context;

class SubCategoryListWithStaticBlocks extends View
{
    /**
     * @var CustomCategory
     */
    public $customCategory;

    public function __construct(
        Context $context,
        SubCategories $subCategories,
        CustomCategory $customCategory,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $subCategories,
            $data);
        $this->customCategory = $customCategory;
    }

    /**
     * @return array
     */
    public function getStaticBlocksForSubCategoryList()
    {
        $category = $this->customCategory->getCategory();
        $staticBlocks = $category->getData('static_block');

        if ($staticBlocks) {
            $staticBlocksExploded = explode('|',$staticBlocks);
            return $staticBlocksExploded;
        } else {
            return [];
        }
    }
}