<?php

namespace Limitless\Delivery\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class MetapackCarrierSorting extends AbstractDb
{
    const ID_FIELD = 'id';
    const TABLE = 'limitless_delivery_metapackcarriersorting';

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