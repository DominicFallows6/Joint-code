<?php

namespace Limitless\TrustpilotApi\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class TrustpilotCache extends AbstractDb
{
    const ID_FIELD = 'id';
    const TABLE = 'trustpilot_cached_data';

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