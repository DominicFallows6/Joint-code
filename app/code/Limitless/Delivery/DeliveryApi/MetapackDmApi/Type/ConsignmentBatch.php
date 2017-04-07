<?php

namespace Limitless\Delivery\DeliveryApi\MetapackDmApi\Type;

class ConsignmentBatch {
    public $allocationFilter; // AllocationFilter
    public $bookingCode; // string
    public $calculateTaxAndDuty; // boolean
    public $consignment; // Consignment
}