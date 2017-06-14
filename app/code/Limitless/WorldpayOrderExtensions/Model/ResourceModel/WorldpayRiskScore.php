<?php

namespace Limitless\WorldpayOrderExtensions\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class WorldpayRiskScore extends AbstractDb
{
    const ID_FIELD = 'id';
    const TABLE = 'worldpay_risk_score';

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE, self::ID_FIELD);
    }
}