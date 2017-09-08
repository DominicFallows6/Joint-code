<?php

namespace Limitless\WorldpayOrderExtensions\Plugin;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;

class UpdateOrderInformationAndNotesPlugin
{
    public function afterPlace(Order $subject, Order $returnOrder)
    {
        /** @var Payment $payment */
        $payment = $returnOrder->getPayment();

        $worldPaySiteCodeName = 'worldpaySiteCode';
        if($payment->hasAdditionalInformation($worldPaySiteCodeName))
        {
            $siteCode = $payment->getAdditionalInformation($worldPaySiteCodeName);
            $returnOrder->addStatusHistoryComment("Worldpay Order MID (SiteCode): $siteCode");
            $payment->unsAdditionalInformation($worldPaySiteCodeName);

            $returnOrder->setPayment($payment);
            $returnOrder->save();
        }

        return $returnOrder;
    }
}