<?php

namespace Limitless\Delivery\Test\Integration\Plugin;
use Limitless\Delivery\Model\AllocationFilter;
use Limitless\Delivery\Plugin\SetAllocationFilterOnShippingAddressPlugin;
use Limitless\Delivery\Plugin\SetPlaceOrderIdPlugin;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\ShippingAddressManagementInterface;
/**
 * @magentoDbIsolation enabled
 */
class SetAllocationFilterOnShippingAddressPluginTest extends \PHPUnit_Framework_TestCase
{
    protected $cartID = '123';
    public function testPluginSavesAllocationFilter()
    {
        $subject= $this->getMock(ShippingAddressManagementInterface::class);
        $proceed = function(){};
        $cartId = $this->cartID;
        $address = $this->getMock(AddressInterface::class);
        $address->method('getPostcode')->willReturn('I don\'t Care');
        /** @var SetAllocationFilterOnShippingAddressPlugin $plugin */
        $plugin = ObjectManager::getInstance()->create(SetAllocationFilterOnShippingAddressPlugin::class);
        $plugin->aroundAssign($subject, $proceed, $cartId, $address);
        /** @var \Limitless\Delivery\Model\AllocationFilter $allocationFilter */
        $allocationFilter = ObjectManager::getInstance()->create(AllocationFilter::class);
        $allocationFilter->getResource()->load($allocationFilter, $cartId, AllocationFilter::QUOTE_ID);
        $this->assertNotNull($allocationFilter->getId(), "NO AllocationFilter Code for $cartId exists");
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