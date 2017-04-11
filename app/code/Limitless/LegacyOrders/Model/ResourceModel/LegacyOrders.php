<?php


namespace Limitless\LegacyOrders\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class LegacyOrders extends AbstractDb {

    const ID_FIELD = 'id';
    const LEGACY_ORDER_ID_FIELD = 'legacy_order_id';
    const TABLE = 'limitless_legacy_orders';
    const EMAIL_FIELD = 'user_email';

    protected function _construct()
    {
        $this->_init(self::TABLE, self::ID_FIELD);
    }

}