<?php

namespace Limitless\ReindexRanging\Catalog\Model\Indexer\Category\Product\Action;

use Magento\Catalog\Model\Indexer\Category\Product\Action\Full as FullIndex;

class Full extends FullIndex
{

    public function isRangingNeeded() {
        return false;
    }
}