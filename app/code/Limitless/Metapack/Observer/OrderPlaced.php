<?php

namespace Limitless\Metapack\Observer;
use Magento\Framework\Event\ObserverInterface;
use Limitless\Metapack\Helper\Service\AllocationService;
use Limitless\Metapack\Helper\Data;
use Psr\Log\LoggerInterface as Logger;

class OrderPlaced implements ObserverInterface
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    protected $logger;
    protected $order;
    protected $helper;

    /**
     * Maybe better to load order object through injection but see below
     * Magento\GiftCard\Observer - does the loading this way
     * @param Logger $logger
     */
    public function __construct(Logger $logger, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Limitless\Metapack\Helper\Data $helper)
    {
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->helper = $helper;
    }

    public function getMetapackConfig($data)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return $this->scopeConfig->getValue('carriers/metapack/'.$data, $storeScope);
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $event = $observer->getEvent();

        /** @var \Magento\Sales\Model\Order $order */
        $order = $event->getOrder();

        $shippingMethod = explode('_',$order->getShippingMethod())[1];

        $parcelDetails = array();
        $parcelDetails['value'] = $order->getBaseSubtotal();
        $parcelDetails['weight'] = $order->getWeight();

        // In line with M1 and BO, add everything into 1 parcel. Solvitt will split if necessary
        $parcels = array(1 => $this->helper->buildParcel($parcelDetails));

        $shippingAddress = $order->getShippingAddress();

        $request = array(
            'first_name'        => $shippingAddress->getFirstname(),
            'last_name'         => $shippingAddress->getLastname(),
            'dest_street'       => $shippingAddress->getStreetLine(1),
            'dest_city'         => $shippingAddress->getCity(),
            'dest_postcode'     => $shippingAddress->getPostcode(),
            'dest_country_id'   => $shippingAddress->getCountryId(),
            'phone'             => $shippingAddress->getTelephone(),
            'package_weight'    => $order->getWeight(),
            'order_number'      => $order->getIncrementId(),
            'value'             => $order->getBaseSubtotal()
        );

        $metapackWsdl = $this->getMetapackConfig('wsdl');
        $metapackUsername = $this->getMetapackConfig('username');
        $metapackPassword = $this->getMetapackConfig('password');

        $allocationService = new AllocationService($metapackWsdl.'AllocationService?wsdl',array("login" => $metapackUsername, "password" => $metapackPassword));
        $consignments = $allocationService->createAndAllocateConsignmentsWithBookingCode(array($this->helper->buildConsignment($request,$parcels)),$shippingMethod,0);
    }

}