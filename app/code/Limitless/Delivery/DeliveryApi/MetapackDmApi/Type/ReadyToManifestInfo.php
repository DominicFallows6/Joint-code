<?php

namespace Limitless\Delivery\DeliveryApi\MetapackDmApi\Type;

class ReadyToManifestInfo {
    public $carrierCode; // string
    public $consignmentCount; // int
    public $manifestGroupCode; // string
    public $parcelCount; // int
}