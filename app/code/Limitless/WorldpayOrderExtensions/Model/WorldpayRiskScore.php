<?php

namespace Limitless\WorldpayOrderExtensions\Model;

use Magento\Framework\Model\AbstractModel;

class WorldpayRiskScore extends AbstractModel
{
    const ORDER_ID = 'order_id';
    const RISK_SCORE = 'risk_score';

    protected function _construct()
    {
        $this->_init(ResourceModel\WorldpayRiskScore::class);
    }
}