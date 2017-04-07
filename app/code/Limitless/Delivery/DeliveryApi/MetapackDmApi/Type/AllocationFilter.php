<?php

namespace Limitless\Delivery\DeliveryApi\MetapackDmApi\Type;

class AllocationFilter {
    public $acceptableCarrierCodes; // ArrayOf_soapenc_string
    public $acceptableCarrierServiceCodes; // ArrayOf_soapenc_string
    public $acceptableCarrierServiceGroupCodes; // ArrayOf_soapenc_string
    public $acceptableCarrierServiceTypeCodes; // ArrayOf_soapenc_string
    public $acceptableCollectionDays; // WorkingDays
    public $acceptableCollectionSlots; // ArrayOf_tns1_DateRange
    public $acceptableDeliveryDays; // WorkingDays
    public $acceptableDeliverySlots; // ArrayOf_tns1_DateRange
    public $allocationSchemeCode; // string
    public $expandGroups; // boolean
    public $filterGroup1; // int
    public $filterGroup2; // int
    public $filterGroup3; // int
    public $firstCollectionOnly; // boolean
    public $maxAnalysisDayCount; // int
    public $maxCost; // double
    public $maxDatesPerService; // int
    public $maxScore; // double
    public $minScore; // double
    public $preFilterSortOrder1; // int
    public $preFilterSortOrder2; // int
    public $preFilterSortOrder3; // int
    public $sortOrder1; // int
    public $sortOrder2; // int
    public $sortOrder3; // int
    public $unacceptableCarrierCodes; // ArrayOf_soapenc_string
    public $unacceptableCarrierServiceCodes; // ArrayOf_soapenc_string
    public $unacceptableCarrierServiceGroupCodes; // ArrayOf_soapenc_string
    public $unacceptableCarrierServiceTypeCodes; // ArrayOf_soapenc_string
    public $unacceptableCollectionDays; // WorkingDays
    public $unacceptableCollectionSlots; // ArrayOf_tns1_DateRange
    public $unacceptableDeliveryDays; // WorkingDays
    public $unacceptableDeliverySlots; // ArrayOf_tns1_DateRange
}