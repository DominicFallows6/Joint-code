<?php

namespace Limitless\Delivery\DeliveryApi\MetapackDmApi\Type;

class Parcel {
    public $CODParcelAmount; // double
    public $cartonId; // string
    public $code; // string
    public $destinationReference; // string
    public $dutyPaid; // double
    public $number; // int
    public $originReference; // string
    public $outerConsignmentCode; // string
    public $outerParcelNumber; // int
    public $packageTypeCode; // string
    public $parcelDepth; // double
    public $parcelHeight; // double
    public $parcelPrintStatus; // string
    public $parcelValue; // double
    public $parcelWeight; // double
    public $parcelWidth; // double
    public $products; // ArrayOf_tns1_Product
    public $recipientReference; // string
    public $reference; // string
    public $senderReference; // string
    public $trackingCode; // string
    public $trackingUrl; // string
}