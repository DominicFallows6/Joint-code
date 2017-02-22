<?php

namespace Limitless\Delivery\Test\Integration;

use Magento\Catalog\Model\Product;
use Magento\TestFramework\ObjectManager;
use PHPUnit_Framework_TestCase;

//TODO: PHPStorm running too slow to work using this file. Put into other integration test then move into here. Possibly need to upgrade my PHPStorm

class ConsignmentAttributeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return RateRequest
     */
    private function buildRateRequest()
    {
        /** @var \Magento\Quote\Model\Quote\Address\RateRequest $rateRequest */
        $rateRequest = $this->objectManager->create(RateRequest::class);
        $rateRequest->setPackageValue(99.99);
        $rateRequest->setPackageWeight(5);
        $rateRequest->setDestStreet('26 Fell View');
        $rateRequest->setDestCity('Burnley');
        $rateRequest->setDestPostcode('BB10 2SF');
        $rateRequest->setDestCountryId('GBR');
        return $rateRequest;
    }
    
    public function testFindServiceGroupsUsingPalletAttribute()
    {
        $rateRequest = $this->buildRateRequest();
        $this->find->setResponseDeliveryOptionsType('ggg');
        $options = $this->find->call($rateRequest);

        print_r(json_encode($options));

        foreach($options as $option) {
            echo $option['deliveryServiceLevelString'] . ' ' . $option['deliveryTimeString'].PHP_EOL;
        }

        $this->assertNotEmpty($options);
    }
}