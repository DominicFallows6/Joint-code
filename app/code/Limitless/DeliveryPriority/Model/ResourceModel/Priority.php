<?php

namespace Limitless\DeliveryPriority\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Priority extends AbstractDb
{
    const ID_FIELD = 'id';
    const TABLE = 'limitless_deliverypriority';

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