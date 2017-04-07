<?php

namespace Limitless\Delivery\DeliveryApi\MetapackDmApi\Type;

class ConsignmentTrackingInfo {
    public $carrierConsignmentCode; // string
    public $consignmentCode; // string
    public $parcels; // ArrayOf_tns1_ParcelTrackingInfo
}