<?php

namespace Limitless\Delivery\DeliveryApi\MetapackDmApi\Service;

class InformationService extends \SoapClient {

    /**
     *
     *
     * @param
     * @return ArrayOf_tns1_CodeName
     */
    public function findWarehouses() {
        return $this->__soapCall('findWarehouses', array(),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param
     * @return ArrayOf_soapenc_string
     */
    public function findTransactionTypes() {
        return $this->__soapCall('findTransactionTypes', array(),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param
     * @return ArrayOf_soapenc_string
     */
    public function findConsignmentStatuses() {
        return $this->__soapCall('findConsignmentStatuses', array(),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param
     * @return ArrayOf_soapenc_string
     */
    public function findManifestStatuses() {
        return $this->__soapCall('findManifestStatuses', array(),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param
     * @return ArrayOf_tns1_CodeName
     */
    public function findCarriers() {
        return $this->__soapCall('findCarriers', array(),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param
     * @return ArrayOf_tns1_CodeName
     */
    public function findCarrierServiceTypes() {
        return $this->__soapCall('findCarrierServiceTypes', array(),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param
     * @return ArrayOf_tns1_CodeNameDescription
     */
    public function findGroups() {
        return $this->__soapCall('findGroups', array(),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $carrierCode
     * @return ArrayOf_tns1_CarrierService
     */
    public function findCarrierServices($carrierCode) {
        return $this->__soapCall('findCarrierServices', array($carrierCode),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param
     * @return ArrayOf_soapenc_string
     */
    public function findPODTypes() {
        return $this->__soapCall('findPODTypes', array(),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param ArrayOf_tns1_Address $addresses
     * @return ArrayOf_tns1_VerifiedAddress
     */
    public function verifyAddresses($addresses) {
        return $this->__soapCall('verifyAddresses', array($addresses),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param Address $address
     * @return ArrayOf_tns1_Address
     */
    public function findSimilarAddresses(Address $address) {
        return $this->__soapCall('findSimilarAddresses', array($address),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

}