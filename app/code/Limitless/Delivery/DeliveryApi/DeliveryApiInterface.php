<?php

namespace Limitless\Delivery\DeliveryApi;

use Magento\Quote\Model\Quote\Address\RateRequest;

interface DeliveryApiInterface
{
    public function buildRequest(RateRequest $request);

    public function call(RateRequest $request): array;

    public function filterResponse($deliveryOptions, $orderValue): array;
}