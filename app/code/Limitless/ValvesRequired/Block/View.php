<?php

namespace Limitless\ValvesRequired\Block;

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

    public function hasValvesRequired():bool
    {
        return (bool) $this->getValvesRequiredCmsBlockId();
    }

    public function getValvesRequiredHtml()
    {
        return $this->hasValvesRequired() ?
            $this->getValvesRequiredBlock()->toHtml():
            '';
    }

    private function getValvesRequiredCmsBlockId()
    {
        $product = $this->getCurrentProduct();
        if ($product && $product->getCustomAttribute('valves_required')) {
            return $product->getAttributeText('valves_required');
        } else {
            return "";
        }
    }

    /**
     * @return Product|null
     */
    public function getCurrentProduct()
    {
        return $this->registry->registry('product');
    }

    private function getValvesRequiredBlock():Block
    {
        /** @var Block $block */
        $block = $this->getLayout()->createBlock(Block::class);
        $block->setData('block_id', $this->getValvesRequiredCmsBlockId());
        return $block;
    }
}