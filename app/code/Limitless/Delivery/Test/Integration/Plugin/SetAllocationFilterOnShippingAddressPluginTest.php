<?php

namespace Limitless\Delivery\Test\Integration\Plugin;
use Limitless\Delivery\Model\AllocationFilter;
use Limitless\Delivery\Plugin\SetAllocationFilterOnQuoteAddressResourcePlugin;
use Limitless\Delivery\Plugin\SetPlaceOrderIdPlugin;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ResourceModel\Quote\Address;
use Magento\Quote\Model\Quote\Address as AddressModel;
use Magento\Quote\Model\ShippingAddressManagementInterface;
/**
 * @magentoDbIsolation enabled
 */
class SetAllocationFilterOnShippingAddressPluginTest extends \PHPUnit_Framework_TestCase
{
    protected $cartID = '123';
    private $shippingMethod = 'acceptableCarrierServiceGroupCodes:ECONOMY';

    public function testPluginSavesAllocationFilter()
    {
        /** @var Address $subject */
        $subject = $this->getMockBuilder(Address::class)
            ->disableOriginalConstructor()
            ->getMock();
        $proceed = function(){};
        $cartId = $this->cartID;
        /** @var AddressModel|\PHPUnit_Framework_MockObject_MockObject $addressModel */
        $addressModel = $this->getMockBuilder(AddressModel::class)
            ->setMethods(array_merge(get_class_methods(AddressModel::class), ['getAddressType']))
            ->disableOriginalConstructor()
            ->getMock();
        $addressModel->method('getAddressType')->willReturn(AddressModel::ADDRESS_TYPE_SHIPPING);
        $addressModel->method('getShippingMethod')->willReturn($this->shippingMethod);
        $quote = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->getMock();
        $quote->method('getId')->willReturn($this->cartID);
        $addressModel->method('getQuote')->willReturn($quote);

        /** @var SetAllocationFilterOnQuoteAddressResourcePlugin $plugin */
        $plugin = ObjectManager::getInstance()->create(SetAllocationFilterOnQuoteAddressResourcePlugin::class);
        $plugin->aroundSave($subject, $proceed, $addressModel);
        /** @var \Limitless\Delivery\Model\AllocationFilter $allocationFilter */
        $allocationFilter = ObjectManager::getInstance()->create(AllocationFilter::class);
        $allocationFilter->getResource()->load($allocationFilter, $cartId, AllocationFilter::QUOTE_ID);
        $this->assertNotNull($allocationFilter->getId(), "NO AllocationFilter Code for quote $cartId exists");
    }
    
    public function testAllocationFilterIsSaved()
    {
        $subject = $this->getMock(CartManagementInterface::class);
        $proceed = function(){return 3;};
        /** @var \Limitless\Delivery\Model\AllocationFilter $allocationFilter */
        $allocationFilterModel = ObjectManager::getInstance()->create(\Limitless\Delivery\Model\AllocationFilter::class);
        $allocationFilterModel->setData(AllocationFilter::QUOTE_ID, $this->cartID);
        $allocationFilterModel->setData(AllocationFilter::ALLOCATION_FILTER, uniqid('dummy-'));
        $allocationFilterModel->getResource()->save($allocationFilterModel);
        /** @var SetPlaceOrderIdPlugin $plugin */
        $plugin = ObjectManager::getInstance()->create(SetPlaceOrderIdPlugin::class);
        $orderID = $plugin->aroundPlaceOrder($subject, $proceed, $this->cartID);
        $allocationFilterModel2 = ObjectManager::getInstance()->create(\Limitless\Delivery\Model\AllocationFilter::class);
        $allocationFilterModel2->getResource()->load($allocationFilterModel2, $orderID, AllocationFilter::ORDER_ID);
        $this->assertNotNull($allocationFilterModel2->getId(), "Cannot find allocation code order for ".$orderID);
    }
}