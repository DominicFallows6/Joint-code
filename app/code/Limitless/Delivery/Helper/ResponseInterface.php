<?php

namespace Limitless\Delivery\Helper;

interface ResponseInterface
{
    public function buildPremiumDeliveryOption(array $deliveryOption, array $filteredDeliveryOptions): array;

    public function buildEconomyDeliveryOption(int $orderValue, array $economyOption): array;
}