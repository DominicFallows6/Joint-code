<?php

namespace Limitless\TagManagerDataLayer\Test\Unit\Helper;

use Limitless\TagManagerDataLayer\Helper\DataLayerBlockLocator;
use Magento\Framework\ObjectManagerInterface;

class DataLayerBlockLocatorTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockObjectManager;

    protected function setUp()
    {
        $this->mockObjectManager = $this->getMock(ObjectManagerInterface::class);
    }

    private function getInstance(array $affiliateBlockPool = [])
    {
        return new DataLayerBlockLocator($this->mockObjectManager, $affiliateBlockPool);
    }

    /**
     * @dataProvider invalidAffiliateCodeDataProvider
     */
    public function testThrowsExceptionOnInvalidAffiliateCode($invalidAffiliateCode, $expected)
    {
        $expectedMessage = sprintf('The code is invalid: "%s"', $expected);
        $this->setExpectedException(\InvalidArgumentException::class, $expectedMessage);
        $this->getInstance()->locate($invalidAffiliateCode);
    }

    public function invalidAffiliateCodeDataProvider()
    {
        return [
            [null, 'NULL'],
            ['', ''],
            [123, '123'],
        ];
    }

    /**
     * @dataProvider validAffiliateCodeDataProvider
     */
    public function testThrowsExceptionIfNoMatchingAffiliateBlockIsRegistered($validAffiliateCode)
    {
        $expectedMessage = sprintf('No block registered for code "%s"', $validAffiliateCode);
        $this->setExpectedException(\InvalidArgumentException::class, $expectedMessage);
        $this->getInstance()->locate($validAffiliateCode);
    }

    public function validAffiliateCodeDataProvider()
    {
        return [
            ['foo'],
            ['bar'],
        ];
    }

    public function testReturnsInstanceOfRegisteredClass()
    {
        $affiliateBlockClass = get_class($this);
        $this->mockObjectManager->method('create')->with($affiliateBlockClass)->willReturn($this);
        $affiliateCode = 'test';
        $result = $this->getInstance([$affiliateCode => $affiliateBlockClass])->locate($affiliateCode);
        $this->assertInstanceOf($affiliateBlockClass, $result);
    }

    public function testIsValidRetunsFalseIfNoMatchingBlockIsRegistered()
    {
        $this->assertFalse($this->getInstance()->isValid('test'));
    }

    public function testIsValidRetunsTrueIfMatchingBlockIsRegistered()
    {
        $this->assertTrue($this->getInstance(['test' => __CLASS__])->isValid('test'));
    }
}