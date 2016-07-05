<?php

namespace Limitless\Metapack\Helper\Type;

class ConsignmentTrackingInfo {
    public $carrierConsignmentCode; // string
    public $consignmentCode; // string
    public $parcels; // ArrayOf_tns1_ParcelTrackingInfo
}