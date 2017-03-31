<?php

namespace Limitless\TopTips\Block;

use Magento\Catalog\Model\Product;
use Magento\Cms\Block\Block;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;


class View extends Template
{
    /**
     * @var Registry
     */
    protected $registry;

    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
    }

    public function hasTopTips()
    {
        return (bool) $this->getProductTopTipsCmsBlockId();
    }

    public function getTopTipsHtml()
    {
        return $this->hasTopTips() ?
            $this->getTopTipsCmsBlock()->toHtml():
            '';
    }

    private function getProductTopTipsCmsBlockId()
    {
        $product = $this->getCurrentProduct();
        if ($product && $product->getAttributeText('product_top_tips')) {
            return $product->getAttributeText('product_top_tips');
        } else {
            return "";
        }
    }

    /**
     * @return Product|null
     */
    private function getCurrentProduct()
    {
        return $this->registry->registry('product');
    }

    private function getTopTipsCmsBlock()
    {
        /** @var Block $block */
        $block = $this->getLayout()->createBlock(Block::class);
        $block->setData('block_id', $this->getProductTopTipsCmsBlockId());
        return $block;
    }
}