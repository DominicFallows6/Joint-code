<?php

namespace Limitless\Delivery\Test\Integration;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\TestFramework\ObjectManager;
use Limitless\Delivery\DeliveryApi\MetapackDmApi;
use Magento\Quote\Model\Quote\Address\RateRequest;

class MetapackDmApiCallTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var MetapackDmApi
     */
    private $metapackDmApi;

    public function setUp()
    {
        $this->objectManager = ObjectManager::getInstance();

        $scopeConfigMock = $this->getMock(ScopeConfigInterface::class);
        $scopeConfigMock->method('getValue')->willReturnCallback([$this, 'scopeConfigGetValue']);

        $this->metapackDmApi = $this->objectManager->create(MetapackDmApi::class, ['scopeConfig' => $scopeConfigMock]);
    }

    public function scopeConfigGetValue($path)
    {
        $pathMap = [
            'carriers/delivery/warehouse_code' => 'HR',
            'carriers/delivery/username' => 'soap_ts',
            'carriers/delivery/password' => 'd1lb3rt75',
            'carriers/delivery/wsdl' => 'https://dm.metapack.com/api/5.x/services/',
            'carriers/delivery/premium_groups' => 'NEXTDAY,NEXTDAY12,NEXTDAY930,SAT930,SATURDAYPM,SATURDAYAM',
            'carriers/delivery/economy_group' => 'ECONOMY',
            'general/store_information/phone' => '01282 471385',
            'general/store_information/name' => 'Limitless Digital',
            'general/store_information/country_id' => 'GB',
            'general/store_information/street_line1' => 'Units 1 and 2 Dawson Court',
            'general/store_information/street_line2' => 'Billington Road',
            'general/store_information/city' => 'Burnley',
            'general/store_information/postcode' => 'BB11 5UB',
            'general/store_information/region_id' => 'Lancashire'
        ];

        return $pathMap[$path] ?? null;
    }

    /**
     * @return RateRequest
     */
    private function buildRateRequest()
    {
        /** @var \Magento\Quote\Model\Quote\Address\RateRequest $rateRequest */
        $rateRequest = $this->objectManager->create(RateRequest::class);
        $rateRequest->setPackageValue(7.99);
        $rateRequest->setPackageWeight(0.06);
        $rateRequest->setDestStreet('Unit 1 and 2');
        $rateRequest->setDestCity('Barcelona');
        $rateRequest->setDestPostcode('08041');
        $rateRequest->setDestCountryId('ESP');
//        $rateRequest->setDestStreet('26 Fell View');
//        $rateRequest->setDestCity('Burnley');
//        $rateRequest->setDestPostcode('BB10 2SF');
//        $rateRequest->setDestCountryId('GBR');
        return $rateRequest;
    }

    public function testFindServiceGroups()
    {
        $rateRequest = $this->buildRateRequest();
        $options = $this->metapackDmApi->call($rateRequest);

        foreach($options as $option) {
            echo $option['carrierServiceCode'] . PHP_EOL;
        }

        $this->assertNotEmpty($options);
    }

}