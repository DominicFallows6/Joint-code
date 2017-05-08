<?php
/**
 * Created by PhpStorm.
 * User: tprocter
 * Date: 13/04/2017
 * Time: 16:05
 */

namespace Limitless\CheckoutPaymentInstructions\Model;


use Magento\OfflinePayments\Model\Banktransfer;

class BanktransferCheckoutInstructions extends Banktransfer
{
    /**
     * Get checkout instructions text from config
     *
     * @return string
     */
    public function getCheckoutInstructions()
    {
        return trim($this->getConfigData('checkout_instructions'));
    }
}