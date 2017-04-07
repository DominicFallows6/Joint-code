<?php

namespace Limitless\Delivery\DeliveryApi\MetapackDmApi\Service;

class ConsignmentTrackingService extends \SoapClient {

    /**
     *
     *
     * @param string $orderReference
     * @return ArrayOf_tns1_ConsignmentTrackingInfo
     */
    public function findParcelTrackingByOrderReference($orderReference) {
        return $this->__soapCall('findParcelTrackingByOrderReference', array($orderReference),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param string $consignmentCode
     * @return ArrayOf_tns1_ConsignmentTrackingInfo
     */
    public function findParcelTrackingByConsignmentCode($consignmentCode) {
        return $this->__soapCall('findParcelTrackingByConsignmentCode', array($consignmentCode),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param dateTime $fromDate
     * @param dateTime $toDate
     * @return ArrayOf_tns1_ParcelStatusHistory
     */
    public function findAllParcelStatusesBetweenDates($fromDate, $toDate) {
        return $this->__soapCall('findAllParcelStatusesBetweenDates', array($fromDate, $toDate),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

}