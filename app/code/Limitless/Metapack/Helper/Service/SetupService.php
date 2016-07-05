<?php

namespace Limitless\Metapack\Helper\Service;

class SetupService extends \SoapClient {

    /**
     *
     *
     * @param ArrayOf_soapenc_string $carrierCodes
     * @return boolean
     */
    public function enableCarriers($carrierCodes) {
        return $this->__soapCall('enableCarriers', array($carrierCodes),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param ArrayOf_soapenc_string $carrierCodes
     * @return boolean
     */
    public function disableCarriers($carrierCodes) {
        return $this->__soapCall('disableCarriers', array($carrierCodes),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param ArrayOf_soapenc_string $carrierServiceCodes
     * @return boolean
     */
    public function enableCarrierServices($carrierServiceCodes) {
        return $this->__soapCall('enableCarrierServices', array($carrierServiceCodes),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

    /**
     *
     *
     * @param ArrayOf_soapenc_string $carrierServiceCodes
     * @return boolean
     */
    public function disableCarrierServices($carrierServiceCodes) {
        return $this->__soapCall('disableCarrierServices', array($carrierServiceCodes),       array(
                'uri' => 'urn:DeliveryManager/services',
                'soapaction' => ''
            )
        );
    }

}