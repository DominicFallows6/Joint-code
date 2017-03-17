<?php

namespace Limitless\Delivery\Plugin;

use Limitless\Delivery\Model\AllocationFilter;
use Limitless\Delivery\Model\AllocationFilterFactory;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class AddDeliveryFiltersToOrderApiPlugin
{
    /**
     * @var OrderExtensionFactory
     */
    private $orderExtensionAttributesFactory;
    /**
     * @var AllocationFilterFactory
     */
    
    private $allocationFilterFactory;
    
    public function __construct(
        OrderExtensionFactory $orderExtensionAttributesFactory,
        AllocationFilterFactory $allocationFilterFactory
    ) {
        $this->orderExtensionAttributesFactory = $orderExtensionAttributesFactory;
        $this->allocationFilterFactory = $allocationFilterFactory;
    }
    
    public function aroundGet(OrderRepositoryInterface $subject, \Closure $proceed, $orderId)
    {
        /** @var OrderInterface $order */
        $order = $proceed($orderId);
        $this->addExtensionAttributesToOrder($orderId, $order);
        return $order;
    }
    
    public function aroundGetList(OrderRepositoryInterface $subject, \Closure $proceed, $searchCriteria)
    {
        /** @var \Magento\Sales\Api\Data\OrderSearchResultInterface $searchResults */
        $searchResults = $proceed($searchCriteria);
        $orders = $searchResults->getItems();
        foreach ($orders AS $key=>$order) {
            $this->addExtensionAttributesToOrder($order->getEntityId(), $order);
        }
        return $searchResults;
    }
    
    public function afterSave(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        $this->addExtensionAttributesToOrder($order->getEntityId(), $order);
        return $order;
    }
    
    /**
     * @param $orderId
     * @param $order
     */
    protected function addExtensionAttributesToOrder($orderId, $order)
    {
        /** @var AllocationFilter $allocationFilter */
        $allocationFilter = $this->allocationFilterFactory->create();
        $allocationFilter->getResource()->load($allocationFilter, $orderId, AllocationFilter::ORDER_ID);
        if ($allocationFilter->getId()) {
            $extensionAttributes = $order->getExtensionAttributes();
            if (!$extensionAttributes) {
                $extensionAttributes = $this->orderExtensionAttributesFactory->create();
                $order->setExtensionAttributes($extensionAttributes);
            }

            $allocationFilterString = $allocationFilter->getData(AllocationFilter::ALLOCATION_FILTER);
            $extensionAttributes->setAllocationFilter($allocationFilterString);

            $dateToShip = $this->extractDateFromAllocationFilter($allocationFilterString, 'Collection');
            $dateToDeliver = $this->extractDateFromAllocationFilter($allocationFilterString, 'Delivery');

            if($dateToShip !== '' && $dateToDeliver !== '') {     // If customer has selected a delivery date
                $extensionAttributes->setDateToShip($dateToShip);
                $extensionAttributes->setDateToDeliver($dateToDeliver);
            }

        }
    }

    private function extractDateFromAllocationFilter($allocationFilter, $shipmentSlotType = 'Collection')
    {
        $date = false;
        $datePieces = [];
        if(in_array($shipmentSlotType, ['Collection','Delivery'])) {
            preg_match('/acceptable' . $shipmentSlotType . 'Slots:(\d+-\d+-\d+)/', $allocationFilter, $datePieces);
            if (!empty($datePieces)) {
                $date = $datePieces[1];
            }
        }
        return $date;
    }
}