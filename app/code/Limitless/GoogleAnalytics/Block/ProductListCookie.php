<?php

namespace Limitless\GoogleAnalytics\Block;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\Category;

class ProductListCookie extends Template
{
    /** @var Category */
    private $category;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->category = $registry->registry('current_category');
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getCategoryName()
    {
        return $this->escapeHtml($this->category->getName());
    }
}