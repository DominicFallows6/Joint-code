<?php

namespace Limitless\Metapack\Helper\Service;

class DebugService extends \SoapClient {

    /**
     *
     *
     * @param string $consignmentCode
     * @param AllocationFilter $filter
     * @return ArrayOf_soapenc_string
     */
    public function debugConsignmentWhyNot($consignmentCode, AllocationFilter $filter) {
        return $this->__soapCall('debugConsignmentWhyNot', array($consignmentCode, $filter),       array(
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
     * @return ArrayOf_soapenc_string
     */
    public function debugConsignmentWhyNotWithBookingCode($consignmentCode, $bookingCode) {
        return $this->__soapCall('debugConsignmentWhyNotWithBookingCode', array($consignmentCode, $bookingCode),       array(
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
     * @return ArrayOf_soapenc_string
     */
    public function debugWhyNotWithBookingCode(Consignment $consignment, $bookingCode) {
        return $this->__soapCall('debugWhyNotWithBookingCode', array($consignment, $bookingCode),       array(
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
     * @return ArrayOf_soapenc_string
     */
    public function debugWhyNot(Consignment $consignment, AllocationFilter $filter) {
        return $this->__soapCall('debugWhyNot', array($consignment, $filter),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

}