<?php

namespace Limitless\ApiImageBypass\Plugin;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Image\Cache;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;

class ApiBypassProductImageCache
{
    /** @var State */
    private $state;

    public function __construct(State $state)
    {
        $this->state = $state;
    }

    /**
     * @param Cache $subject
     * @param \Closure $proceed
     * @param Product $product
     * @return Cache|mixed
     */
    public function aroundGenerate(Cache $subject, \Closure $proceed, Product $product)
    {
        if ($this->state->getAreaCode() == Area::AREA_WEBAPI_REST)
        {
            return $subject;
        }

        return $proceed($product);
    }
}