<?php

namespace Limitless\Delivery\DeliveryApi\MetapackDmApi\Service;

use Limitless\Delivery\DeliveryApi\MetapackDmApi\Type\Consignment;
use Limitless\Delivery\DeliveryApi\MetapackDmApi\Type\AllocationFilter;

class AllocationService extends \SoapClient {

    /**
     *
     *
     * @param ArrayOf_soapenc_string $consignmentCodes
     * @return ArrayOf_soapenc_string
     */
    public function deallocate($consignmentCodes) {
        return $this->__soapCall('deallocate', array($consignmentCodes),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param Consignment $consignment
     * @param AllocationFilter $filter
     * @param boolean $calculateTaxAndDuty
     * @return ArrayOf_tns1_DeliveryOption
     */
    public function findDeliveryOptions(Consignment $consignment, AllocationFilter $filter, $calculateTaxAndDuty) {
        return $this->__soapCall('findDeliveryOptions', array($consignment, $filter, $calculateTaxAndDuty),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param Consignment $consignment
     * @param string $bookingCode
     * @param boolean $calculateTaxAndDuty
     * @return ArrayOf_tns1_DeliveryOption
     */
    public function findDeliveryOptionsWithBookingCode(Consignment $consignment, $bookingCode, $calculateTaxAndDuty) {
        return $this->__soapCall('findDeliveryOptionsWithBookingCode', array($consignment, $bookingCode, $calculateTaxAndDuty),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $consignmentCode
     * @param AllocationFilter $filter
     * @param boolean $calculateTaxAndDuty
     * @return ArrayOf_tns1_DeliveryOption
     */
    public function findDeliveryOptionsForConsignment($consignmentCode, AllocationFilter $filter, $calculateTaxAndDuty) {
        return $this->__soapCall('findDeliveryOptionsForConsignment', array($consignmentCode, $filter, $calculateTaxAndDuty),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $consignmentCode
     * @param string $bookingCode
     * @param boolean $calculateTaxAndDuty
     * @return ArrayOf_tns1_DeliveryOption
     */
    public function findDeliveryOptionsForConsignmentWithBookingCode($consignmentCode, $bookingCode, $calculateTaxAndDuty) {
        return $this->__soapCall('findDeliveryOptionsForConsignmentWithBookingCode', array($consignmentCode, $bookingCode, $calculateTaxAndDuty),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param ArrayOf_tns1_Consignment $consignments
     * @param AllocationFilter $filter
     * @param boolean $calculateTaxAndDuty
     * @return ArrayOf_tns1_Consignment
     */
    public function createAndAllocateConsignments($consignments, AllocationFilter $filter, $calculateTaxAndDuty) {
        return $this->__soapCall('createAndAllocateConsignments', array($consignments, $filter, $calculateTaxAndDuty),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param ArrayOf_tns1_Consignment $consignments
     * @param string $bookingCode
     * @param boolean $calculateTaxAndDuty
     * @return ArrayOf_tns1_Consignment
     */
    public function createAndAllocateConsignmentsWithBookingCode($consignments, $bookingCode, $calculateTaxAndDuty) {
        return $this->__soapCall('createAndAllocateConsignmentsWithBookingCode', array($consignments, $bookingCode, $calculateTaxAndDuty),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param ArrayOf_tns1_ConsignmentBatch $consignmentBatch
     * @return ArrayOf_tns1_ConsignmentBatchResults
     */
    public function batchCreateAndAllocateConsignments($consignmentBatch) {
        return $this->__soapCall('batchCreateAndAllocateConsignments', array($consignmentBatch),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $consignmentCode
     * @param AllocationFilter $filter
     * @param boolean $recalculateTaxAndDuty
     * @return Consignment
     */
    public function verifyAllocation($consignmentCode, AllocationFilter $filter, $recalculateTaxAndDuty) {
        return $this->__soapCall('verifyAllocation', array($consignmentCode, $filter, $recalculateTaxAndDuty),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param ArrayOf_soapenc_string $consignmentCodes
     * @param AllocationFilter $filter
     * @param boolean $calculateTaxAndDuty
     * @return ArrayOf_tns1_Consignment
     */
    public function allocateConsignments($consignmentCodes, AllocationFilter $filter, $calculateTaxAndDuty) {
        return $this->__soapCall('allocateConsignments', array($consignmentCodes, $filter, $calculateTaxAndDuty),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param ArrayOf_soapenc_string $consignmentCodes
     * @param string $bookingCode
     * @param boolean $calculateTaxAndDuty
     * @return ArrayOf_tns1_Consignment
     */
    public function allocateConsignmentsWithBookingCode($consignmentCodes, $bookingCode, $calculateTaxAndDuty) {
        return $this->__soapCall('allocateConsignmentsWithBookingCode', array($consignmentCodes, $bookingCode, $calculateTaxAndDuty),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param Consignment $consignment
     * @param AllocationFilter $allocationFilter
     * @param boolean $calculateTaxAndDuty
     * @param ArrayOf_tns1_Property $parameters
     * @return DespatchedConsignment
     */
    public function despatchConsignment(Consignment $consignment, AllocationFilter $allocationFilter, $calculateTaxAndDuty, $parameters) {
        return $this->__soapCall('despatchConsignment', array($consignment, $allocationFilter, $calculateTaxAndDuty, $parameters),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param Consignment $consignment
     * @param string $bookingCode
     * @param boolean $calculateTaxAndDuty
     * @param ArrayOf_tns1_Property $parameters
     * @return DespatchedConsignment
     */
    public function despatchConsignmentWithBookingCode(Consignment $consignment, $bookingCode, $calculateTaxAndDuty, $parameters) {
        return $this->__soapCall('despatchConsignmentWithBookingCode', array($consignment, $bookingCode, $calculateTaxAndDuty, $parameters),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $consignmentCode
     * @param string $bookingCode
     * @param boolean $recalculateTaxAndDuty
     * @return Consignment
     */
    public function verifyAllocationWithBookingCode($consignmentCode, $bookingCode, $recalculateTaxAndDuty) {
        return $this->__soapCall('verifyAllocationWithBookingCode', array($consignmentCode, $bookingCode, $recalculateTaxAndDuty),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

}