<?php

namespace Limitless\TagManagerDataLayer\Helper\AffiliatesDataLayer;

use Limitless\TagManagerDataLayer\Api\AffiliateHelperInterface;
use Magento\Checkout\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\OrderFactory;

class Shopzilla implements AffiliateHelperInterface
{
    const SHOPPINGCOM_GENERAL_SETTINGS_CONFIGPATH = 'google/limitless_tagmanager_datalayer/affiliate_tracking/shopzilla/';

    const SHOPPINGCOM_DATALAYER_NAME = 'shopzilla';

    /** @var string */
    private $shopzillaCustType;

    /** @var string */
    private $shopzillaOrderValue;

    /** @var string */
    private $shopzillaUnitsOrdered;

    /** @var Session */
    protected $checkoutSession;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var OrderInterface */
    private $lastOrder;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var OrderFactory */
    private $orderFactory;

    public function __construct(
        Session $checkoutSession,
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        OrderFactory $orderFactory,
        Context $context
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->scopeConfig = $context->getScopeConfig();
        $this->customerRepository = $customerRepository;
        $this->orderFactory = $orderFactory;
    }

    /**
     * @return array
     */
    public function getAffiliateDataLayer(): array
    {
        $this->initLastOrder();
        $this->buildShopzillaValues();

        return [self::SHOPPINGCOM_DATALAYER_NAME =>
            [
                'custtype' => $this->shopzillaCustType,
                'ordervalue' => $this->shopzillaOrderValue,
                'unitsordered' => $this->shopzillaUnitsOrdered
            ]
        ];
    }

    private function initLastOrder()
    {
        $orderId = $this->checkoutSession->getData('last_order_id');
        $this->lastOrder = $this->orderRepository->get($orderId);
    }

    public function buildShopzillaValues()
    {
        $this->shopzillaCustType = $this->getCustomerOrderType($this->lastOrder->getCustomerEmail());
        $this->shopzillaOrderValue = $this->getShopzillaOrderTotal();
        $this->shopzillaUnitsOrdered = '' . intval($this->lastOrder->getTotalQtyOrdered());
    }

    /**
     * @param $productItem
     * @return string
     */
    private function getShopzillaOrderTotal(): string
    {
        $vatSetting = $this->getShopzillaVATSetting();
        $shippingSetting = $this->getShopzillaShippingSetting();
        $orderTotal = $this->lastOrder->getGrandTotal();

        switch ($vatSetting) {
            case 'exclude':
                $orderTotal -= $this->lastOrder->getTaxAmount();
                break;
        }

        switch ($shippingSetting) {
            case 'exclude':
                $orderTotal -= $this->lastOrder->getShippingAmount();
                break;
        }

        return $this->ukNumberFormat($orderTotal);
    }

    public function getCustomerOrderType($customerEmail)
    {
        //Retrieve all orders with this email address
        $orders = $this->orderFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_email', $customerEmail);

        if (count($orders->getAllIds()) > 1) {
            return '0'; //existing cusotmer
        }
        return '1'; //new customer
    }

    private function ukNumberFormat($number)
    {
        if (is_numeric($number)) {
            return number_format($number, 2, '.', '');
        }
        return '';
    }

    private function getShopzillaGeneralSettingConfig($setting)
    {
        return $this->scopeConfig->getValue(
            self::SHOPPINGCOM_GENERAL_SETTINGS_CONFIGPATH . $setting,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    private function getShopzillaVATSetting()
    {
        return $this->getShopzillaGeneralSettingConfig('shopzilla_total_vat_setting') ?? 'include';
    }

    private function getShopzillaShippingSetting()
    {
        return $this->getShopzillaGeneralSettingConfig('shopzilla_total_shipping_setting') ?? 'include';
    }
}
