<?php

namespace Limitless\Delivery\DeliveryApi\MetapackDmApi\Type;

class Product {
    public $countryOfOrigin; // string
    public $fabricContent; // string
    public $harmonisedProductCode; // string
    public $miscellaneousInfo; // ArrayOf_soapenc_string
    public $productCode; // string
    public $productDescription; // string
    public $productQuantity; // long
    public $productTypeDescription; // string
    public $totalProductValue; // double
    public $unitProductWeight; // double
}