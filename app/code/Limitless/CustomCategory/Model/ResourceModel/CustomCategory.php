<?php

namespace Limitless\CustomCategory\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CustomCategory extends AbstractDb
{
    const ID_FIELD = 'id';
    const CATEGORY_ID_FIELD = 'category_id';
    const TABLE = 'limitless_custom_category';
    const STORE_ID = 'store_id';
    const STATUS = 'status';

    protected function _construct()
    {
        $this->_init(self::TABLE, self::ID_FIELD);
    }
}