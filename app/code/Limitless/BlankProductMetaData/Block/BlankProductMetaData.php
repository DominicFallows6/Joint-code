<?php

namespace Limitless\BlankProductMetaData\Block;

use Magento\Catalog\Block\Product\View;

class BlankProductMetaData extends View
{
    /**
     * Add meta information from product to head block
     *
     * @return \Magento\Catalog\Block\Product\View
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->createBlock('Magento\Catalog\Block\Breadcrumbs');
        $product = $this->getProduct();
        if (!$product) {
            return parent::_prepareLayout();
        }

        $title = $product->getMetaTitle();
        if ($title) {
            $this->pageConfig->getTitle()->set($title);
        }
        $keyword = $product->getMetaKeyword();
        $currentCategory = $this->_coreRegistry->registry('current_category');
        if ($keyword) {
            $this->pageConfig->setKeywords($keyword);
        } elseif ($currentCategory) {
            $this->pageConfig->setKeywords('');
        }
        $description = $product->getMetaDescription();
        if ($description) {
            $this->pageConfig->setDescription($description);
        } else {
            $this->pageConfig->setDescription('');
        }
        if ($this->_productHelper->canUseCanonicalTag()) {
            $this->pageConfig->addRemotePageAsset(
                $product->getUrlModel()->getUrl($product, ['_ignore_category' => true]),
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );
        }

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($product->getName());
        }

        return parent::_prepareLayout();
    }
    protected function _toHtml()
    {
        $this->setModuleName($this->extractModuleName('Magento\Catalog\Block\Product\View'));
        return parent::_toHtml();
    }
}