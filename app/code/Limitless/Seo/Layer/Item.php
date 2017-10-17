<?php

namespace Limitless\Seo\Layer;

use Limitless\Seo\Model\RobotsExclusions;
use Magento\Catalog\Model\Layer\Filter\Item as M2Item;
use Magento\Framework\UrlInterface;
use Magento\Theme\Block\Html\Pager;

class Item extends M2Item
{

    /**
     * @var RobotsExclusions
     */
    private $robotsExclusions;

    public function __construct(
        UrlInterface $url,
        Pager $htmlPagerBlock,
        RobotsExclusions $robotsExclusions,
        array $data = []
    ) {
        parent::__construct($url, $htmlPagerBlock, $data);
        $this->robotsExclusions = $robotsExclusions;
    }

    /**
     * @return bool
     */
    public function isNoFollow()
    {
        $requestVar = $this->getFilter()->getRequestVar();
        return $this->robotsExclusions->shouldAssetBeNoFollow([$requestVar => []]);
    }

}