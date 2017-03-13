<?php


namespace Limitless\Delivery\Test\Integration;

use Limitless\Delivery\Helper\MetapackData;

class DataTransformationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MetapackData
     */
    private $transformation;

    protected function setUp()
    {
        $this->transformation = new MetapackData();
    }

    private $metapackDataFixture = '[{"delivery":{"from":"2017-02-13T08:00:00.000Z","to":"2017-02-13T18:00:59.999Z"},"groupCodes":["ECONOMY"],"address":"","photoUrls":[],"telephoneNumber":null,"carrierServiceName":"OverNight","distance":{"unit":"m","value":0},"postcode":"","fullName":"DX Freight (1 Man) OverNight","description":null,"collection":{"from":"2017-02-10T18:59:00.000Z","to":"2017-02-10T19:59:59.999Z"},"storeId":null,"carrierServiceCode":"NF1ON","long":null,"logoUrl":null,"optionType":"HOME","storeTimes":[],"carrierCode":"NF1MAN","hasDisabledAccess":false,"storeName":"","shippingCharge":0,"bookingCode":"NF1ON\/2017-02-10\/*-*\/*\/*-*","lat":null,"deliveryTimeString":"3 - 5 Working days","allocationFilter":"ECONOMY","deliveryServiceLevelString":"I\'m not in a hurry"},{"delivery":{"from":"2017-02-02T08:00:00.000Z","to":"2017-02-02T12:00:59.999Z"},"groupCodes":["NEXTDAY12"],"address":"","photoUrls":[],"telephoneNumber":null,"carrierServiceName":"Next Day AM","distance":{"unit":"m","value":0},"postcode":"","fullName":"DX Freight (1 Man) Next Day AM","description":null,"collection":{"from":"2017-02-01T18:59:00.000Z","to":"2017-02-01T19:59:59.999Z"},"storeId":null,"carrierServiceCode":"NF1ONNEXTDAYAM","long":null,"logoUrl":null,"optionType":"HOME","storeTimes":[],"carrierCode":"NF1MAN","hasDisabledAccess":false,"storeName":"","shippingCharge":0,"bookingCode":"NF1ONNEXTDAYAM\/2017-02-01\/*-*\/*\/*-*","lat":null,"deliveryTimeString":"AM Before 12:00","deliveryServiceLevelString":"THU  2 FEB","allocationFilter":"NEXTDAY12_2017-02-01_2017-02-02"},{"delivery":{"from":"2017-02-02T08:00:00.000Z","to":"2017-02-02T20:00:59.999Z"},"groupCodes":["NEXTDAY"],"address":"","photoUrls":[],"telephoneNumber":null,"carrierServiceName":"Courier Service 24 Hour Non-Signature","distance":{"unit":"m","value":0},"postcode":"","fullName":"Hermes Courier Service 24 Hour Non-Signature","description":null,"collection":{"from":"2017-02-01T18:30:00.000Z","to":"2017-02-01T19:30:59.999Z"},"storeId":null,"carrierServiceCode":"HERMNONSIGNDG","long":null,"logoUrl":null,"optionType":"HOME","storeTimes":[],"carrierCode":"HERMESPOS","hasDisabledAccess":false,"storeName":"","shippingCharge":0,"bookingCode":"HERMNONSIGNDG\/2017-02-01\/*-*\/*\/*-*","lat":null,"deliveryTimeString":"Anytime Before 20:00","deliveryServiceLevelString":"THU  2 FEB","allocationFilter":"NEXTDAY_2017-02-01_2017-02-02"},{"delivery":{"from":"2017-02-03T08:00:00.000Z","to":"2017-02-03T12:00:59.999Z"},"groupCodes":["NEXTDAY12"],"address":"","photoUrls":[],"telephoneNumber":null,"carrierServiceName":"Next Day AM","distance":{"unit":"m","value":0},"postcode":"","fullName":"DX Freight (1 Man) Next Day AM","description":null,"collection":{"from":"2017-02-02T18:59:00.000Z","to":"2017-02-02T19:59:59.999Z"},"storeId":null,"carrierServiceCode":"NF1ONNEXTDAYAM","long":null,"logoUrl":null,"optionType":"HOME","storeTimes":[],"carrierCode":"NF1MAN","hasDisabledAccess":false,"storeName":"","shippingCharge":0,"bookingCode":"NF1ONNEXTDAYAM\/2017-02-02\/*-*\/*\/*-*","lat":null,"deliveryTimeString":"AM Before 12:00","deliveryServiceLevelString":"FRI  3 FEB","allocationFilter":"NEXTDAY12_2017-02-02_2017-02-03"},{"delivery":{"from":"2017-02-03T08:00:00.000Z","to":"2017-02-03T20:00:59.999Z"},"groupCodes":["NEXTDAY"],"address":"","photoUrls":[],"telephoneNumber":null,"carrierServiceName":"Courier Service 24 Hour Non-Signature","distance":{"unit":"m","value":0},"postcode":"","fullName":"Hermes Courier Service 24 Hour Non-Signature","description":null,"collection":{"from":"2017-02-02T18:30:00.000Z","to":"2017-02-02T19:30:59.999Z"},"storeId":null,"carrierServiceCode":"HERMNONSIGNDG","long":null,"logoUrl":null,"optionType":"HOME","storeTimes":[],"carrierCode":"HERMESPOS","hasDisabledAccess":false,"storeName":"","shippingCharge":0,"bookingCode":"HERMNONSIGNDG\/2017-02-02\/*-*\/*\/*-*","lat":null,"deliveryTimeString":"Anytime Before 20:00","deliveryServiceLevelString":"FRI  3 FEB","allocationFilter":"NEXTDAY_2017-02-02_2017-02-03"},{"delivery":{"from":"2017-02-04T08:00:00.000Z","to":"2017-02-04T18:00:59.999Z"},"groupCodes":["SAT12"],"address":"","photoUrls":[],"telephoneNumber":null,"carrierServiceName":"OverNight Saturday","distance":{"unit":"m","value":0},"postcode":"","fullName":"DX Freight (1 Man) OverNight Saturday","description":null,"collection":{"from":"2017-02-01T18:30:00.000Z","to":"2017-02-01T19:30:59.999Z"},"storeId":null,"carrierServiceCode":"NF1ONSAT","long":null,"logoUrl":null,"optionType":"HOME","storeTimes":[],"carrierCode":"NF1MAN","hasDisabledAccess":false,"storeName":"","shippingCharge":0,"bookingCode":"NF1ONSAT\/2017-02-01\/*-*\/*\/*-*","lat":null,"deliveryTimeString":"Anytime Before 18:00","deliveryServiceLevelString":"SAT  4 FEB","allocationFilter":"SAT12_2017-02-01_2017-02-04"},{"delivery":{"from":"2017-02-04T08:00:00.000Z","to":"2017-02-04T20:00:59.999Z"},"groupCodes":["NEXTDAY"],"address":"","photoUrls":[],"telephoneNumber":null,"carrierServiceName":"Courier Service 24 Hour Non-Signature","distance":{"unit":"m","value":0},"postcode":"","fullName":"Hermes Courier Service 24 Hour Non-Signature","description":null,"collection":{"from":"2017-02-03T18:30:00.000Z","to":"2017-02-03T19:30:59.999Z"},"storeId":null,"carrierServiceCode":"HERMNONSIGNDG","long":null,"logoUrl":null,"optionType":"HOME","storeTimes":[],"carrierCode":"HERMESPOS","hasDisabledAccess":false,"storeName":"","shippingCharge":0,"bookingCode":"HERMNONSIGNDG\/2017-02-03\/*-*\/*\/*-*","lat":null,"deliveryTimeString":"Anytime Before 20:00","deliveryServiceLevelString":"SAT  4 FEB","allocationFilter":"NEXTDAY_2017-02-03_2017-02-04"},{"delivery":{"from":"2017-02-06T08:00:00.000Z","to":"2017-02-06T12:00:59.999Z"},"groupCodes":["NEXTDAY12"],"address":"","photoUrls":[],"telephoneNumber":null,"carrierServiceName":"Next Day AM","distance":{"unit":"m","value":0},"postcode":"","fullName":"DX Freight (1 Man) Next Day AM","description":null,"collection":{"from":"2017-02-03T18:59:00.000Z","to":"2017-02-03T19:59:59.999Z"},"storeId":null,"carrierServiceCode":"NF1ONNEXTDAYAM","long":null,"logoUrl":null,"optionType":"HOME","storeTimes":[],"carrierCode":"NF1MAN","hasDisabledAccess":false,"storeName":"","shippingCharge":0,"bookingCode":"NF1ONNEXTDAYAM\/2017-02-03\/*-*\/*\/*-*","lat":null,"deliveryTimeString":"AM Before 12:00","deliveryServiceLevelString":"MON  6 FEB","allocationFilter":"NEXTDAY12_2017-02-03_2017-02-06"},{"delivery":{"from":"2017-02-06T08:00:00.000Z","to":"2017-02-06T18:00:59.999Z"},"groupCodes":["NEXTDAY"],"address":"","photoUrls":[],"telephoneNumber":null,"carrierServiceName":"OverNight","distance":{"unit":"m","value":0},"postcode":"","fullName":"DX Freight (1 Man) OverNight","description":null,"collection":{"from":"2017-02-03T18:59:00.000Z","to":"2017-02-03T19:59:59.999Z"},"storeId":null,"carrierServiceCode":"NF1ON","long":null,"logoUrl":null,"optionType":"HOME","storeTimes":[],"carrierCode":"NF1MAN","hasDisabledAccess":false,"storeName":"","shippingCharge":6.95,"bookingCode":"NF1ON\/2017-02-03\/*-*\/*\/*-*","lat":null,"deliveryTimeString":"Anytime Before 18:00","deliveryServiceLevelString":"MON  6 FEB","allocationFilter":"NEXTDAY_2017-02-03_2017-02-06"},{"delivery":{"from":"2017-02-07T08:00:00.000Z","to":"2017-02-07T12:00:59.999Z"},"groupCodes":["NEXTDAY12"],"address":"","photoUrls":[],"telephoneNumber":null,"carrierServiceName":"Next Day AM","distance":{"unit":"m","value":0},"postcode":"","fullName":"DX Freight (1 Man) Next Day AM","description":null,"collection":{"from":"2017-02-06T18:59:00.000Z","to":"2017-02-06T19:59:59.999Z"},"storeId":null,"carrierServiceCode":"NF1ONNEXTDAYAM","long":null,"logoUrl":null,"optionType":"HOME","storeTimes":[],"carrierCode":"NF1MAN","hasDisabledAccess":false,"storeName":"","shippingCharge":0,"bookingCode":"NF1ONNEXTDAYAM\/2017-02-06\/*-*\/*\/*-*","lat":null,"deliveryTimeString":"AM Before 12:00","deliveryServiceLevelString":"TUE  7 FEB","allocationFilter":"NEXTDAY12_2017-02-06_2017-02-07"},{"delivery":{"from":"2017-02-07T08:00:00.000Z","to":"2017-02-07T20:00:59.999Z"},"groupCodes":["NEXTDAY"],"address":"","photoUrls":[],"telephoneNumber":null,"carrierServiceName":"Courier Service 24 Hour Non-Signature","distance":{"unit":"m","value":0},"postcode":"","fullName":"Hermes Courier Service 24 Hour Non-Signature","description":null,"collection":{"from":"2017-02-06T18:30:00.000Z","to":"2017-02-06T19:30:59.999Z"},"storeId":null,"carrierServiceCode":"HERMNONSIGNDG","long":null,"logoUrl":null,"optionType":"HOME","storeTimes":[],"carrierCode":"HERMESPOS","hasDisabledAccess":false,"storeName":"","shippingCharge":0,"bookingCode":"HERMNONSIGNDG\/2017-02-06\/*-*\/*\/*-*","lat":null,"deliveryTimeString":"Anytime Before 20:00","deliveryServiceLevelString":"TUE  7 FEB","allocationFilter":"NEXTDAY_2017-02-06_2017-02-07"},{"delivery":{"from":"2017-02-08T08:00:00.000Z","to":"2017-02-08T12:00:59.999Z"},"groupCodes":["NEXTDAY12"],"address":"","photoUrls":[],"telephoneNumber":null,"carrierServiceName":"Next Day AM","distance":{"unit":"m","value":0},"postcode":"","fullName":"DX Freight (1 Man) Next Day AM","description":null,"collection":{"from":"2017-02-07T18:59:00.000Z","to":"2017-02-07T19:59:59.999Z"},"storeId":null,"carrierServiceCode":"NF1ONNEXTDAYAM","long":null,"logoUrl":null,"optionType":"HOME","storeTimes":[],"carrierCode":"NF1MAN","hasDisabledAccess":false,"storeName":"","shippingCharge":0,"bookingCode":"NF1ONNEXTDAYAM\/2017-02-07\/*-*\/*\/*-*","lat":null,"deliveryTimeString":"AM Before 12:00","deliveryServiceLevelString":"WED  8 FEB","allocationFilter":"NEXTDAY12_2017-02-07_2017-02-08"},{"delivery":{"from":"2017-02-08T08:00:00.000Z","to":"2017-02-08T20:00:59.999Z"},"groupCodes":["NEXTDAY"],"address":"","photoUrls":[],"telephoneNumber":null,"carrierServiceName":"Courier Service 24 Hour Non-Signature","distance":{"unit":"m","value":0},"postcode":"","fullName":"Hermes Courier Service 24 Hour Non-Signature","description":null,"collection":{"from":"2017-02-07T18:30:00.000Z","to":"2017-02-07T19:30:59.999Z"},"storeId":null,"carrierServiceCode":"HERMNONSIGNDG","long":null,"logoUrl":null,"optionType":"HOME","storeTimes":[],"carrierCode":"HERMESPOS","hasDisabledAccess":false,"storeName":"","shippingCharge":0,"bookingCode":"HERMNONSIGNDG\/2017-02-07\/*-*\/*\/*-*","lat":null,"deliveryTimeString":"Anytime Before 20:00","deliveryServiceLevelString":"WED  8 FEB","allocationFilter":"NEXTDAY_2017-02-07_2017-02-08"},{"delivery":{"from":"2017-02-09T08:00:00.000Z","to":"2017-02-09T12:00:59.999Z"},"groupCodes":["NEXTDAY12"],"address":"","photoUrls":[],"telephoneNumber":null,"carrierServiceName":"Next Day AM","distance":{"unit":"m","value":0},"postcode":"","fullName":"DX Freight (1 Man) Next Day AM","description":null,"collection":{"from":"2017-02-08T18:59:00.000Z","to":"2017-02-08T19:59:59.999Z"},"storeId":null,"carrierServiceCode":"NF1ONNEXTDAYAM","long":null,"logoUrl":null,"optionType":"HOME","storeTimes":[],"carrierCode":"NF1MAN","hasDisabledAccess":false,"storeName":"","shippingCharge":0,"bookingCode":"NF1ONNEXTDAYAM\/2017-02-08\/*-*\/*\/*-*","lat":null,"deliveryTimeString":"AM Before 12:00","deliveryServiceLevelString":"THU  9 FEB","allocationFilter":"NEXTDAY12_2017-02-08_2017-02-09"},{"delivery":{"from":"2017-02-09T08:00:00.000Z","to":"2017-02-09T20:00:59.999Z"},"groupCodes":["NEXTDAY"],"address":"","photoUrls":[],"telephoneNumber":null,"carrierServiceName":"Courier Service 24 Hour Non-Signature","distance":{"unit":"m","value":0},"postcode":"","fullName":"Hermes Courier Service 24 Hour Non-Signature","description":null,"collection":{"from":"2017-02-08T18:30:00.000Z","to":"2017-02-08T19:30:59.999Z"},"storeId":null,"carrierServiceCode":"HERMNONSIGNDG","long":null,"logoUrl":null,"optionType":"HOME","storeTimes":[],"carrierCode":"HERMESPOS","hasDisabledAccess":false,"storeName":"","shippingCharge":0,"bookingCode":"HERMNONSIGNDG\/2017-02-08\/*-*\/*\/*-*","lat":null,"deliveryTimeString":"Anytime Before 20:00","deliveryServiceLevelString":"THU  9 FEB","allocationFilter":"NEXTDAY_2017-02-08_2017-02-09"},{"delivery":{"from":"2017-02-10T08:00:00.000Z","to":"2017-02-10T12:00:59.999Z"},"groupCodes":["NEXTDAY12"],"address":"","photoUrls":[],"telephoneNumber":null,"carrierServiceName":"Next Day AM","distance":{"unit":"m","value":0},"postcode":"","fullName":"DX Freight (1 Man) Next Day AM","description":null,"collection":{"from":"2017-02-09T18:59:00.000Z","to":"2017-02-09T19:59:59.999Z"},"storeId":null,"carrierServiceCode":"NF1ONNEXTDAYAM","long":null,"logoUrl":null,"optionType":"HOME","storeTimes":[],"carrierCode":"NF1MAN","hasDisabledAccess":false,"storeName":"","shippingCharge":0,"bookingCode":"NF1ONNEXTDAYAM\/2017-02-09\/*-*\/*\/*-*","lat":null,"deliveryTimeString":"AM Before 12:00","deliveryServiceLevelString":"FRI 10 FEB","allocationFilter":"NEXTDAY12_2017-02-09_2017-02-10"},{"delivery":{"from":"2017-02-10T08:00:00.000Z","to":"2017-02-10T20:00:59.999Z"},"groupCodes":["NEXTDAY"],"address":"","photoUrls":[],"telephoneNumber":null,"carrierServiceName":"Courier Service 24 Hour Non-Signature","distance":{"unit":"m","value":0},"postcode":"","fullName":"Hermes Courier Service 24 Hour Non-Signature","description":null,"collection":{"from":"2017-02-09T18:30:00.000Z","to":"2017-02-09T19:30:59.999Z"},"storeId":null,"carrierServiceCode":"HERMNONSIGNDG","long":null,"logoUrl":null,"optionType":"HOME","storeTimes":[],"carrierCode":"HERMESPOS","hasDisabledAccess":false,"storeName":"","shippingCharge":0,"bookingCode":"HERMNONSIGNDG\/2017-02-09\/*-*\/*\/*-*","lat":null,"deliveryTimeString":"Anytime Before 20:00","deliveryServiceLevelString":"FRI 10 FEB","allocationFilter":"NEXTDAY_2017-02-09_2017-02-10"},{"delivery":{"from":"2017-02-11T08:00:00.000Z","to":"2017-02-11T18:00:59.999Z"},"groupCodes":["SAT12"],"address":"","photoUrls":[],"telephoneNumber":null,"carrierServiceName":"OverNight Saturday","distance":{"unit":"m","value":0},"postcode":"","fullName":"DX Freight (1 Man) OverNight Saturday","description":null,"collection":{"from":"2017-02-08T18:30:00.000Z","to":"2017-02-08T19:30:59.999Z"},"storeId":null,"carrierServiceCode":"NF1ONSAT","long":null,"logoUrl":null,"optionType":"HOME","storeTimes":[],"carrierCode":"NF1MAN","hasDisabledAccess":false,"storeName":"","shippingCharge":0,"bookingCode":"NF1ONSAT\/2017-02-04\/*-*\/*\/*-*","lat":null,"deliveryTimeString":"Anytime Before 18:00","deliveryServiceLevelString":"SAT 11 FEB","allocationFilter":"SAT12_2017-02-08_2017-02-11"},{"delivery":{"from":"2017-02-11T08:00:00.000Z","to":"2017-02-11T20:00:59.999Z"},"groupCodes":["NEXTDAY"],"address":"","photoUrls":[],"telephoneNumber":null,"carrierServiceName":"Courier Service 24 Hour Non-Signature","distance":{"unit":"m","value":0},"postcode":"","fullName":"Hermes Courier Service 24 Hour Non-Signature","description":null,"collection":{"from":"2017-02-10T18:30:00.000Z","to":"2017-02-10T19:30:59.999Z"},"storeId":null,"carrierServiceCode":"HERMNONSIGNDG","long":null,"logoUrl":null,"optionType":"HOME","storeTimes":[],"carrierCode":"HERMESPOS","hasDisabledAccess":false,"storeName":"","shippingCharge":0,"bookingCode":"HERMNONSIGNDG\/2017-02-10\/*-*\/*\/*-*","lat":null,"deliveryTimeString":"Anytime Before 20:00","deliveryServiceLevelString":"SAT 11 FEB","allocationFilter":"NEXTDAY_2017-02-10_2017-02-11"}]';

    public function testCalculateDaysDifference()
    {
        $this->assertSame(2, $this->transformation->calculateDateDifference('2017-02-01', '2017-02-03'));
        $this->assertSame(3, $this->transformation->calculateDateDifference('2017-02-01', '2017-02-04'));
        $this->assertSame(0, $this->transformation->calculateDateDifference('2017-02-01', '2017-02-01'));
        $this->assertSame(-1, $this->transformation->calculateDateDifference('2017-02-01', '2017-01-31'));
    }

    public function testAddsDaysFromTodayToMetapackResult()
    {
        $input = array_map([$this->transformation,'addDate'],json_decode($this->metapackDataFixture,true));
        $result = array_map([$this->transformation,'addDaysFromToday'],$input);

        foreach($result as $option) {
            $this->assertArrayHasKey('daysFromToday',$option['delivery']);
        }
    }

    public function testAddsDateToMetapackResult()
    {
        $result = array_map([$this->transformation,'addDate'],json_decode($this->metapackDataFixture,true));

        foreach($result as $option) {
            $this->assertArrayHasKey('date',$option['delivery']);
        }
    }

    public function testFillsInNonReturnedDays()
    {
        $mpData = array_map([$this->transformation, 'addDaysFromToday'],
            array_map([$this->transformation, 'addDate'], json_decode($this->metapackDataFixture, true))
        );

        $groupedDays = $this->transformation->groupByDaysFromToday($mpData);

        $this->assertSame([1,2,3,4,5,6,7,8,9,10,11,12], array_keys($this->transformation->fillGaps($groupedDays)));

    }

    public function testGroupsByDaysFromToday()
    {
        $input = [
            ['delivery' => ['daysFromToday' => 1]],
            ['delivery' => ['daysFromToday' => 1]],
            ['delivery' => ['daysFromToday' => 3]]
        ];

        $expected = [
            1 => [
                ['delivery' => ['daysFromToday' => 1]],
                ['delivery' => ['daysFromToday' => 1]]
            ],
            3 => [
                ['delivery' => ['daysFromToday' => 3]]
            ]
        ];

        $this->assertSame($expected, $this->transformation->groupByDaysFromToday($input));

        $mpData = array_map([$this->transformation, 'addDaysFromToday'],
            array_map([$this->transformation, 'addDate'], json_decode($this->metapackDataFixture, true))
        );

        $this->assertSame([1,2,3,5,6,7,8,9,10,12],array_keys($this->transformation->groupByDaysFromToday($mpData)));
    }

    public function testReturnsMaxOptionsPerDay()
    {
        $this->assertSame(2,$this->transformation->getMaxOptionsPerDay(json_decode($this->metapackDataFixture, true)));
    }

}