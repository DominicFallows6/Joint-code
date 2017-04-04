<?php
/**
 * Created by PhpStorm.
 * User: tprocter
 * Date: 31/03/2017
 * Time: 13:10
 */

namespace Limitless\LocalStorageCacheFix\Plugin;

use Magento\Checkout\Model\Session;

class InvalidateCartPlugin
{
    public function afterClearQuote(Session $subject, Session $result)
    {
        $result->setLoadInactive(false);
        $result->replaceQuote($result->getQuote()->save());
        return $result;
    }
}