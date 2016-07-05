<?php

namespace Limitless\Metapack\Helper\Type;

class DeliveryOption {
    public $bookingCode; // string
    public $carrierCode; // string
    public $carrierCustom1; // string
    public $carrierCustom2; // string
    public $carrierCustom3; // string
    public $carrierServiceCode; // string
    public $carrierServiceTypeCode; // string
    public $collectionSlots; // ArrayOf_tns1_DateRange
    public $collectionWindow; // DateRange
    public $cutOffDateTime; // dateTime
    public $deliveryLocation; // string
    public $deliverySlots; // ArrayOf_tns1_DateRange
    public $deliveryWindow; // DateRange
    public $groupCodes; // ArrayOf_soapenc_string
    public $name; // string
    public $nominatableCollectionSlot; // boolean
    public $nominatableDeliverySlot; // boolean
    public $recipientTimeZone; // string
    public $score; // double
    public $senderTimeZone; // string
    public $shippingCharge; // double
    public $shippingCost; // double
    public $taxAndDuty; // double
    public $taxAndDutyStatusText; // string
    public $vatRate; // double
}