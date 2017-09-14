<?php

namespace Limitless\Delivery\Plugin;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Shipping;
use Magento\Store\Model\App\Emulation;

class TranslateDeliveryOptionsInAdminPlugin
{
    /**
     * @var Emulation
     */
    private $emulation;

    public function __construct(Emulation $emulation)
    {
        $this->emulation = $emulation;
    }

    public function aroundCollectRates(
        Shipping $subject,
        \Closure $proceed,
        RateRequest $request
    ) {

        $this->emulation->startEnvironmentEmulation($request->getStoreId());
        $result = $proceed($request);
        $this->emulation->stopEnvironmentEmulation();

        return $result;
    }
}