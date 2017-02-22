<?php

namespace Limitless\Delivery\DeliveryApi;

interface DeliveryApiInterface
{
    public function buildRequest($data);

    public function call($request);

    public function filterResponse($deliveryOptions, $orderValue);
}