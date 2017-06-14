<?php

namespace Limitless\WorldpayOrderExtensions\Model\ResourceModel\WorldpayRiskScore;

use Limitless\WorldpayOrderExtensions\Model\ResourceModel\WorldpayRiskScore as WorldpayRiskScoreResource;
use Limitless\WorldpayOrderExtensions\Model\WorldpayRiskScore;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(WorldpayRiskScore::class, WorldpayRiskScoreResource::class);
    }
}