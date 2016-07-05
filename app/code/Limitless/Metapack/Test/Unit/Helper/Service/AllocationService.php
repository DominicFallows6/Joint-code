<?php

namespace Limitless\Metapack\Test\Unit\Model;

use Limitless\Metapack\Helper\Service\AllocationService;
use Limitless\Metapack\Helper\Data;

class AllocationTest extends \PHPUnit_Framework_TestCase
{
    /*public $consignmentWeight = 0.00;

    public function setUp()
    {
        $this->consignmentWeight = 0.5;
    }

    public function testAllocationService()
    {
        $request = array(
            'dest_street'       => '26 Fell View',
            'dest_city'         => 'Burnley',
            'dest_postcode'     => 'BB10 2SF',
            'dest_country_id'   => 'GB',
            'package_weight'    => $this->consignmentWeight
        );

        $allocationService = new AllocationService('https://dm.metapack.com/api/5.x/services/AllocationService?wsdl',array("login" => 'soap_ts', "password" => 'd1lb3rt75'));
        $deliveryOptions = $allocationService->findDeliveryOptions(Data::buildConsignment($request),Data::buildAllocationFilter(),0);

        //echo PHP_EOL;
        foreach($deliveryOptions as $deliveryOption) {
            //echo $deliveryOption->carrierCode . ' ' . $deliveryOption->name . ' (' . $deliveryOption->carrierServiceCode . ') = £' . number_format($deliveryOption->shippingCost,2) . PHP_EOL;
        }

        //$this->assertNotFalse($deliveryOptions);
    }*/

}