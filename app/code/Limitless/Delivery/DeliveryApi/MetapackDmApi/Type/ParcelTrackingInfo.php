<?php

namespace Limitless\Delivery\DeliveryApi\MetapackDmApi\Type;

class ParcelTrackingInfo {
    public $code; // string
    public $items; // ArrayOf_tns1_ParcelTrackingItem
    public $number; // int
    public $parcelStatusName; // string
    public $statusText; // string
}