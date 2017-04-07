<?php

namespace Limitless\Delivery\DeliveryApi\MetapackDmApi\Type;

class ParcelStatusHistory {
    public $achievedDateTime; // dateTime
    public $carrierReasonCode; // string
    public $carrierStatusCode; // string
    public $consignmentCode; // string
    public $depotAchievingStatus; // string
    public $parcelStatusDesc; // string
    public $parcelStatusText; // string
    public $timeApplied; // dateTime
}