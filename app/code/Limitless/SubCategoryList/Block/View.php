<?php

namespace Limitless\SubCategoryList\Block;

use Limitless\SubCategoryList\Model\SubCategories;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

class View extends Template
{
    /**
     * @var SubCategories
     */
    private $subCategories;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        Context $context,
        SubCategories $subCategories,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $context->getScopeConfig();
        $this->subCategories = $subCategories;
    }

    private function getConfig($path)
    {
        return $this->scopeConfig->getValue('general/limitless_sub_category_list/' . $path, ScopeInterface::SCOPE_STORE);
    }

    private function getConfiguredAdminLevels()
    {
        $configValues = $this->getConfig('limitless_sub_category_list_level');
        $categoryLevels = explode(',', $configValues);
        return $categoryLevels;
    }

    /**
     * @return \Magento\Catalog\Model\Category[]
     */
    public function getCurrentCategoryChildren()
    {
        return $this->shouldDisplayCurrentCategoryChildren() ?
            $this->subCategories->getCurrentCategoryChildren() :
            [];
    }

    private function shouldDisplayCurrentCategoryChildren(): bool
    {
        return in_array($this->subCategories->getCurrentCategoryLevel(), $this->getConfiguredAdminLevels());
    }
}
