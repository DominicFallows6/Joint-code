<?php

namespace Limitless\Delivery\DeliveryApi\MetapackDmApi\Service;

class ConsignmentService extends \SoapClient {

    /**
     *
     *
     * @param string $consignmentCode
     * @param ArrayOf_tns1_UpdateField $updateFields
     * @return Consignment
     */
    public function update($consignmentCode, $updateFields) {
        return $this->__soapCall('update', array($consignmentCode, $updateFields),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $consignmentCode
     * @return TaxAndDuty
     */
    public function calculateTaxAndDuty($consignmentCode) {
        return $this->__soapCall('calculateTaxAndDuty', array($consignmentCode),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $consignmentCode
     * @return boolean
     */
    public function deleteConsignment($consignmentCode) {
        return $this->__soapCall('deleteConsignment', array($consignmentCode),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param ArrayOf_tns1_Consignment $consignments
     * @return boolean
     */
    public function validateConsignments($consignments) {
        return $this->__soapCall('validateConsignments', array($consignments),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param ArrayOf_tns1_Consignment $consignments
     * @return ArrayOf_tns1_Consignment
     */
    public function createConsignments($consignments) {
        return $this->__soapCall('createConsignments', array($consignments),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param ArrayOf_tns1_Consignment $consignments
     * @return ArrayOf_tns1_Consignment
     */
    public function updateConsignments($consignments) {
        return $this->__soapCall('updateConsignments', array($consignments),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param ArrayOf_soapenc_string $consignmentCodes
     * @return boolean
     */
    public function markConsignmentsAsReadyToManifest($consignmentCodes) {
        return $this->__soapCall('markConsignmentsAsReadyToManifest', array($consignmentCodes),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param ArrayOf_soapenc_string $consignmentCodes
     * @return boolean
     */
    public function markConsignmentsAsPrinted($consignmentCodes) {
        return $this->__soapCall('markConsignmentsAsPrinted', array($consignmentCodes),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $consignmentCode
     * @param ArrayOf_tns1_Parcel $parcels
     * @param boolean $recalculateTaxAndDuty
     * @return ArrayOf_tns1_Parcel
     */
    public function appendParcelsToConsignment($consignmentCode, $parcels, $recalculateTaxAndDuty) {
        return $this->__soapCall('appendParcelsToConsignment', array($consignmentCode, $parcels, $recalculateTaxAndDuty),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $consignmentCode
     * @param int $parcelNumber
     * @param ArrayOf_tns1_Product $products
     * @param boolean $recalculateTaxAndDuty
     * @return boolean
     */
    public function packProductsToParcel($consignmentCode, $parcelNumber, $products, $recalculateTaxAndDuty) {
        return $this->__soapCall('packProductsToParcel', array($consignmentCode, $parcelNumber, $products, $recalculateTaxAndDuty),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $consignmentCode
     * @param int $parcelNumber
     * @param ArrayOf_tns1_Product $products
     * @param boolean $recalculateTaxAndDuty
     * @return boolean
     */
    public function unpackProductsFromParcel($consignmentCode, $parcelNumber, $products, $recalculateTaxAndDuty) {
        return $this->__soapCall('unpackProductsFromParcel', array($consignmentCode, $parcelNumber, $products, $recalculateTaxAndDuty),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $innerConsignmentCode
     * @param int $innerParcelNumber
     * @param string $outerConsignmentCode
     * @param int $outerParcelNumber
     * @return boolean
     */
    public function addInnerToOuter($innerConsignmentCode, $innerParcelNumber, $outerConsignmentCode, $outerParcelNumber) {
        return $this->__soapCall('addInnerToOuter', array($innerConsignmentCode, $innerParcelNumber, $outerConsignmentCode, $outerParcelNumber),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $innerConsignmentCode
     * @param int $innerParcelNumber
     * @return boolean
     */
    public function removeInnerFromOuter($innerConsignmentCode, $innerParcelNumber) {
        return $this->__soapCall('removeInnerFromOuter', array($innerConsignmentCode, $innerParcelNumber),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param ArrayOf_soapenc_string $consignmentCodes
     * @param string $manifestGroupCode
     * @return boolean
     */
    public function addConsignmentsToGroup($consignmentCodes, $manifestGroupCode) {
        return $this->__soapCall('addConsignmentsToGroup', array($consignmentCodes, $manifestGroupCode),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param ArrayOf_soapenc_string $consignmentCodes
     * @return boolean
     */
    public function removeConsignmentsFromGroup($consignmentCodes) {
        return $this->__soapCall('removeConsignmentsFromGroup', array($consignmentCodes),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param ArrayOf_soapenc_string $consignmentCodes
     * @param string $reasonCode
     * @param string $reason
     * @return ArrayOf_tns1_ConsignmentActionResult
     */
    public function voidConsignments($consignmentCodes, $reasonCode, $reason) {
        return $this->__soapCall('voidConsignments', array($consignmentCodes, $reasonCode, $reason),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $consignmentCode
     * @return ArrayOf_tns1_AuditRecord
     */
    public function findConsignmentAuditRecords($consignmentCode) {
        return $this->__soapCall('findConsignmentAuditRecords', array($consignmentCode),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $consignmentCode
     * @param int $parcelNo
     * @param boolean $recalculateTaxAndDuty
     * @return boolean
     */
    public function deleteParcelFromConsignment($consignmentCode, $parcelNo, $recalculateTaxAndDuty) {
        return $this->__soapCall('deleteParcelFromConsignment', array($consignmentCode, $parcelNo, $recalculateTaxAndDuty),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $consignmentCode
     * @param string $cartonId
     * @param boolean $recalculateTaxAndDuty
     * @return boolean
     */
    public function deleteParcelFromConsignmentWithCartonId($consignmentCode, $cartonId, $recalculateTaxAndDuty) {
        return $this->__soapCall('deleteParcelFromConsignmentWithCartonId', array($consignmentCode, $cartonId, $recalculateTaxAndDuty),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $orderNumber
     * @param string $manifestGroupCode
     * @return boolean
     */
    public function scanOrderToManifestGroup($orderNumber, $manifestGroupCode) {
        return $this->__soapCall('scanOrderToManifestGroup', array($orderNumber, $manifestGroupCode),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $cartonId
     * @param string $manifestGroupCode
     * @return boolean
     */
    public function scanCartonToManifestGroup($cartonId, $manifestGroupCode) {
        return $this->__soapCall('scanCartonToManifestGroup', array($cartonId, $manifestGroupCode),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $cartonId
     * @return boolean
     */
    public function markCartonReadyToManifest($cartonId) {
        return $this->__soapCall('markCartonReadyToManifest', array($cartonId),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $cartonId
     * @return boolean
     */
    public function markCartonPrinted($cartonId) {
        return $this->__soapCall('markCartonPrinted', array($cartonId),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $consignmentCode
     * @param int $parcelNumber
     * @param ArrayOf_tns1_Property $parameters
     * @return Paperwork
     */
    public function createPaperworkForParcel($consignmentCode, $parcelNumber, $parameters) {
        return $this->__soapCall('createPaperworkForParcel', array($consignmentCode, $parcelNumber, $parameters),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param ArrayOf_soapenc_string $consignmentCodes
     * @param ArrayOf_tns1_Property $parameters
     * @return Paperwork
     */
    public function createPaperworkForConsignments($consignmentCodes, $parameters) {
        return $this->__soapCall('createPaperworkForConsignments', array($consignmentCodes, $parameters),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $consignmentCode
     * @param int $parcelCount
     * @param ArrayOf_tns1_Property $parameters
     * @return Paperwork
     */
    public function createNextPaperworkForConsignment($consignmentCode, $parcelCount, $parameters) {
        return $this->__soapCall('createNextPaperworkForConsignment', array($consignmentCode, $parcelCount, $parameters),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $consignmentCode
     * @param string $cartonId
     * @param ArrayOf_tns1_Property $parameters
     * @return Paperwork
     */
    public function createPaperworkForCarton($consignmentCode, $cartonId, $parameters) {
        return $this->__soapCall('createPaperworkForCarton', array($consignmentCode, $cartonId, $parameters),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

}