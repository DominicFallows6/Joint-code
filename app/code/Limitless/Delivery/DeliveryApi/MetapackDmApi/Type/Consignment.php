<?php

namespace Limitless\Delivery\DeliveryApi\MetapackDmApi\Type;

class Consignment {
    public $CODAmount; // double
    public $CODFlag; // boolean
    public $CODPaymentTypeCode; // string
    public $CODSpecialInstruction; // string
    public $CODSurcharge; // double
    public $alreadyPalletisedGoodsFlag; // boolean
    public $cardNumber; // string
    public $carrierCode; // string
    public $carrierConsignmentCode; // string
    public $carrierName; // string
    public $carrierServiceCode; // string
    public $carrierServiceName; // string
    public $carrierServiceVATRate; // double
    public $cartonNumber; // string
    public $cashOnDeliveryCurrency; // string
    public $committedCollectionWindow; // DateRange
    public $committedDeliveryWindow; // DateRange
    public $consDestinationReference; // string
    public $consOriginReference; // string
    public $consRecipientReference; // string
    public $consReference; // string
    public $consSenderReference; // string
    public $consignmentCode; // string
    public $consignmentLevelDetailsFlag; // boolean
    public $consignmentValue; // double
    public $consignmentValueCurrencyCode; // string
    public $consignmentValueCurrencyRate; // double
    public $consignmentWeight; // double
    public $custom1; // string
    public $custom10; // string
    public $custom2; // string
    public $custom3; // string
    public $custom4; // string
    public $custom5; // string
    public $custom6; // string
    public $custom7; // string
    public $custom8; // string
    public $custom9; // string
    public $customsDocumentationRequired; // boolean
    public $cutOffDate; // dateTime
    public $despatchDate; // dateTime
    public $earliestDeliveryDate; // dateTime
    public $endVatNumber; // string
    public $fragileGoodsFlag; // boolean
    public $guaranteedDeliveryDate; // dateTime
    public $hazardCodes; // ArrayOf_soapenc_string
    public $hazardousGoodsFlag; // boolean
    public $insuranceValue; // double
    public $insuranceValueCurrencyCode; // string
    public $insuranceValueCurrencyRate; // double
    public $languageCode; // string
    public $liquidGoodsFlag; // boolean
    public $manifestGroupCode; // string
    public $maxDimension; // double
    public $metaCampaignKey; // string
    public $metaCustomerKey; // string
    public $moreThanOneMetreGoodsFlag; // boolean
    public $moreThanTwentyFiveKgGoodsFlag; // boolean
    public $orderDate; // dateTime
    public $orderNumber; // string
    public $orderValue; // double
    public $parcelCount; // int
    public $parcels; // ArrayOf_tns1_Parcel
    public $pickTicketNumber; // string
    public $pickupPoint; // string
    public $podRequired; // string
    public $properties; // ArrayOf_tns1_Property
    public $recipientAddress; // Address
    public $recipientCode; // string
    public $recipientContactPhone; // string
    public $recipientEmail; // string
    public $recipientFirstName; // string
    public $recipientLastName; // string
    public $recipientMobilePhone; // string
    public $recipientName; // string
    public $recipientNotificationType; // string
    public $recipientPhone; // string
    public $recipientTimeZone; // string
    public $recipientTitle; // string
    public $recipientVatNumber; // string
    public $returnAddress; // Address
    public $returnEmail; // string
    public $returnFirstName; // string
    public $returnLastName; // string
    public $returnMobile; // string
    public $returnName; // string
    public $returnPhone; // string
    public $returnTitle; // string
    public $senderAddress; // Address
    public $senderCode; // string
    public $senderContactPhone; // string
    public $senderEmail; // string
    public $senderFirstName; // string
    public $senderLastName; // string
    public $senderMobilePhone; // string
    public $senderName; // string
    public $senderNotificationType; // string
    public $senderPhone; // string
    public $senderTimeZone; // string
    public $senderTitle; // string
    public $senderVatNumber; // string
    public $shipmentTypeCode; // string
    public $shippingAccount; // string
    public $shippingCharge; // double
    public $shippingChargeCurrencyCode; // string
    public $shippingChargeCurrencyRate; // double
    public $shippingCost; // double
    public $shippingCostCurrencyCode; // string
    public $shippingCostCurrencyRate; // double
    public $signatoryOnCustoms; // string
    public $specialInstructions1; // string
    public $specialInstructions2; // string
    public $startVatNumber; // string
    public $status; // string
    public $taxAndDuty; // double
    public $taxAndDutyCurrencyCode; // string
    public $taxAndDutyCurrencyRate; // double
    public $taxAndDutyStatusText; // string
    public $taxDutyDeclarationCurrencyCode; // string
    public $termsOfTradeCode; // string
    public $transactionType; // string
    public $twoManLiftFlag; // boolean
}