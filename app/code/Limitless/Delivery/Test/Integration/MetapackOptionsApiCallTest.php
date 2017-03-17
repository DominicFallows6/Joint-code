<?php

namespace Limitless\Delivery\Test\Integration;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\TestFramework\ObjectManager;
use Magento\Framework\App\Helper\Context;
use Limitless\Delivery\DeliveryApi\MetapackApi;
use Magento\Quote\Model\Quote\Address\RateRequest;

class MetapackOptionsApiCallTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var MetapackApi
     */
    private $metapackApi;
    
    public function setUp()
    {
        $this->objectManager = ObjectManager::getInstance();

        $scopeConfigMock = $this->getMock(ScopeConfigInterface::class);
        $scopeConfigMock->method('getValue')->willReturnCallback([$this, 'scopeConfigGetValue']);

        $contextMock = $this->objectManager->create(Context::class, ['scopeConfig' => $scopeConfigMock]);

        $this->metapackApi = $this->objectManager->create(MetapackApi::class, ['context' => $contextMock]);

    }

    public function scopeConfigGetValue($path)
    {
        $pathMap = [
            'carriers/delivery/warehouse_code' => 'DC',
            'carriers/delivery/url' => 'https://dmo.metapack.com',
            'carriers/delivery/key' => 'cf720d4c-edc3-443a-9cf7-febec4e6733b',
            'carriers/delivery/premium_groups' => 'NEXTDAYEVENING,NEXTDAYEARLYMORNING,NEXTDAYMORNING,NEXTDAYAFTERNOON,SATURDAYPM,SUNDAY',
            'carriers/delivery/economy_group' => 'ECONOMY',
            'general/store_information/phone' => '01282 471385',
            'general/store_information/name' => 'Limitless Digital',
            'general/store_information/country_id' => 'GB',
            'general/store_information/street_line1' => 'Units 1 and 2 Dawson Court',
            'general/store_information/street_line2' => 'Billington Road',
            'general/store_information/city' => 'Burnley',
            'general/store_information/postcode' => 'BB11 5UB',
            'general/store_information/region_id' => 'Lancashire',
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
        $rateRequest->setPackageValue(99.99);
        $rateRequest->setPackageWeight(5);
        $rateRequest->setDestStreet('Alpenblick4');
        $rateRequest->setDestCity('Maselheim');
        $rateRequest->setDestPostcode('88437');
        $rateRequest->setDestCountryId('DEU');
        return $rateRequest;
    }
    
    public function testFindServiceGroups()
    {
        $rateRequest = $this->buildRateRequest();
        $options = $this->metapackApi->call($rateRequest);

        foreach($options as $option) {
            $dateToDeliver = [];
            $dateToShip = [];

            preg_match('/acceptableCollectionSlots:(\d+-\d+-\d+)/', $option['allocationFilter'], $dateToShip);
            if(!empty($dateToShip)) {
                echo 'Collection date = ' . $dateToShip[1] . PHP_EOL;
            }

            preg_match('/acceptableDeliverySlots:(\d+-\d+-\d+)/', $option['allocationFilter'], $dateToDeliver);
            if(!empty($dateToDeliver)) {
                echo 'Delivery date = ' . $dateToDeliver[1] . PHP_EOL.PHP_EOL;
            }
        }

        $this->assertNotEmpty($options);
    }

}