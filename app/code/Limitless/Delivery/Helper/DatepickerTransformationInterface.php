<?php
namespace Limitless\Delivery\Helper;

interface DatepickerTransformationInterface
{
    public function getMaxOptionsPerDay(array $data): int;

    public function getDeliveryOptions(array $data): array;
}