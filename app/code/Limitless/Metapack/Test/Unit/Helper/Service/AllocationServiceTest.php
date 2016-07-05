<?php

namespace Limitless\Metapack\Test\Unit\Helper\Service;

use Limitless\Metapack\Helper\Service\AllocationService;
use Limitless\Metapack\Helper\Data;

class AllocationServiceTest extends \PHPUnit_Framework_TestCase
{

    public function testAllocationService()
    {
        $parcelDetails = array();
        $parcelDetails['depth'] = 10;
        $parcelDetails['height'] = 5;
        $parcelDetails['value'] = 9.99;
        $parcelDetails['weight'] = 30.0;
        $parcelDetails['width'] = 5;

        $parcels = array(1 => Data::buildParcel($parcelDetails));

        $request = array(
            'dest_street'       => '26 Fell View',
            'dest_city'         => 'Burnley',
            'dest_postcode'     => 'BB10 2SF',
            'dest_country_id'   => 'GB',
            'package_weight'    => 30.00
        );

        $bookingCode = 'NF1ON';

        $allocationService = new AllocationService('https://dm.metapack.com/api/5.x/services/AllocationService?wsdl',array("login" => 'soap_ts', "password" => 'd1lb3rt75'));
        $consignments = $allocationService->createAndAllocateConsignmentsWithBookingCode(array(Data::buildConsignment($request,$parcels)),$bookingCode,0);

        echo PHP_EOL;
        foreach($consignments as $consignment) {
            print_r($consignment);
        }

        $this->assertNotFalse($consignments);
    }

}