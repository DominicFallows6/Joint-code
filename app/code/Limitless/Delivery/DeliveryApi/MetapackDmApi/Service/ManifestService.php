<?php

namespace Limitless\Delivery\DeliveryApi\MetapackDmApi\Service;

class ManifestService extends \SoapClient {

    /**
     *
     *
     * @param string $carrierCode
     * @param string $warehouseCode
     * @param string $transactionType
     * @param string $manifestGroupCode
     * @param dateTime $despatchDate
     * @param boolean $specificDateOnly
     * @return ArrayOf_soapenc_string
     */
    public function createManifestForFutureDespatch($carrierCode, $warehouseCode, $transactionType, $manifestGroupCode, $despatchDate, $specificDateOnly) {
        return $this->__soapCall('createManifestForFutureDespatch', array($carrierCode, $warehouseCode, $transactionType, $manifestGroupCode, $despatchDate, $specificDateOnly),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $carrierCode
     * @param string $warehouseCode
     * @param string $transactionType
     * @param string $manifestGroupCode
     * @return ArrayOf_soapenc_string
     */
    public function createManifest($carrierCode, $warehouseCode, $transactionType, $manifestGroupCode) {
        return $this->__soapCall('createManifest', array($carrierCode, $warehouseCode, $transactionType, $manifestGroupCode),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $manifestCode
     * @return ArrayOf_tns1_Consignment
     */
    public function findConsignmentsOnManifest($manifestCode) {
        return $this->__soapCall('findConsignmentsOnManifest', array($manifestCode),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $carrierCode
     * @param string $warehouseCode
     * @param string $transactionType
     * @param string $manifestGroupCode
     * @param dateTime $dateFrom
     * @param dateTime $dateTo
     * @return ArrayOf_tns1_Manifest
     */
    public function findManifests($carrierCode, $warehouseCode, $transactionType, $manifestGroupCode, $dateFrom, $dateTo) {
        return $this->__soapCall('findManifests', array($carrierCode, $warehouseCode, $transactionType, $manifestGroupCode, $dateFrom, $dateTo),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $warehouseCode
     * @param string $transactionType
     * @return ArrayOf_tns1_ReadyToManifestInfo
     */
    public function findReadyToManifestRecords($warehouseCode, $transactionType) {
        return $this->__soapCall('findReadyToManifestRecords', array($warehouseCode, $transactionType),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $manifestCode
     * @return boolean
     */
    public function sendManifest($manifestCode) {
        return $this->__soapCall('sendManifest', array($manifestCode),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $manifestCode
     * @return string
     */
    public function createManifestAsPdf($manifestCode) {
        return $this->__soapCall('createManifestAsPdf', array($manifestCode),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $manifestGroupCode
     * @return boolean
     */
    public function markManifestGroupReadyToManifest($manifestGroupCode) {
        return $this->__soapCall('markManifestGroupReadyToManifest', array($manifestGroupCode),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $manifestGroupCode
     * @return boolean
     */
    public function markManifestGroupPrinted($manifestGroupCode) {
        return $this->__soapCall('markManifestGroupPrinted', array($manifestGroupCode),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $oldManifestGroupCode
     * @param string $newManifestGroupCode
     * @return boolean
     */
    public function moveManifestGroup($oldManifestGroupCode, $newManifestGroupCode) {
        return $this->__soapCall('moveManifestGroup', array($oldManifestGroupCode, $newManifestGroupCode),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

}