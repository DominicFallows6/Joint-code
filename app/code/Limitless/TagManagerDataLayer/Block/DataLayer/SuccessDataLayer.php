<?php

namespace Limitless\TagManagerDataLayer\Block\DataLayer;

use Limitless\TagManagerDataLayer\Api\DataLayerAbstract;
use Limitless\TagManagerDataLayer\Helper\AffiliateHelperLocator;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Checkout\Model\Session;
use Limitless\TagManagerDataLayer\Helper\TagsDataLayer\DynamicRemarketing;
use Limitless\TagManagerDataLayer\Helper\TrackingCookie;

class SuccessDataLayer extends DataLayerAbstract
{
    /** @var array */
    private $dataLayerVariables;

    /** @var bool|null|string */
    private $cookieAffiliateCode;

    /** @var array */
    private $affiliateDataLayer;

    /** @var Session */
    private $checkoutSession;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var \Magento\Sales\Api\Data\OrderInterface */
    private $order;

    /** @var DynamicRemarketing */
    private $dynamicRemarketingHelper;

    /** @var ProductFactory */
    private $productFactory;

    /** @var TrackingCookie */
    private $trackingCookieHelper;

    /** @var AffiliateHelperLocator */
    private $affiliateHelperLocator;

    /** @var \Magento\Sales\Model\Order\Item[] */
    private $orderItems;

    public function __construct(
        Context $context,
        Session $checkoutSession,
        OrderRepositoryInterface $orderRepository,
        ProductFactory $productFactory,
        DynamicRemarketing $dynamicRemarketingHelper,
        TrackingCookie $trackingCookieHelper,
        AffiliateHelperLocator $affiliateHelperLocator,
        $data = []
    ) {
        parent::__construct($context, $data);

        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->productFactory = $productFactory;
        $this->dynamicRemarketingHelper = $dynamicRemarketingHelper;
        $this->trackingCookieHelper = $trackingCookieHelper;
        $this->affiliateHelperLocator = $affiliateHelperLocator;
        $this->affiliateDataLayer = false;
        $this->dataLayerVariables = [];
    }

    public function initDataLayerVariables()
    {
        $this->order = $this->getLastOrder();
        $this->orderItems = $this->order->getItems();
        $this->cookieAffiliateCode = $this->getAffiliateCodeFromCookie();

        if ($this->cookieAffiliateCode) {
            $this->checkForValidAffiliateAndInitAffiliateVariables();
        }

        //This includes VAT and Shipping
        $this->dataLayerVariables['totalPayable'] = $this->ukNumberFormat($this->order->getGrandTotal());

        $this->initAffiliateDLVariables();
        $this->initDynamicRemarketingDLVariables();
    }

    /**
     * @return array
     */
    public function getDataLayerVariables(): array
    {
        return $this->dataLayerVariables;
    }

    private function initDynamicRemarketingDLVariables()
    {
        $products = $quantities = [];

        foreach ($this->orderItems as $productItem) {
            $products[] = $productItem->getProduct();
            $quantities[] = $productItem->getQtyOrdered();
        }

        $this->dynamicRemarketingHelper->buildAllDynamicRemarketingValues(
            'purchase',
            $products,
            '',
            $this->getDRTotalValue(),
            $quantities
        );

        $this->mergeIntoDataLayer($this->dynamicRemarketingHelper->getAllDynamicRemarketingValuesInArray());
    }

    private function getDRTotalValue()
    {
        $orderTotal = $this->order->getGrandTotal();
        $vatSetting = $this->dynamicRemarketingHelper->getTotalVatSetting();
        $shippingSetting = $this->dynamicRemarketingHelper->getTotalShippingSetting();

        switch ($vatSetting) {
            case 'exclude':
                $orderTotal -= $this->order->getTaxAmount();
                break;
        }

        switch ($shippingSetting) {
            case 'exclude':
                $orderTotal -= $this->order->getShippingAmount();
                break;
        }

        return $this->ukNumberFormat($orderTotal);
    }

    private function initAffiliateDLVariables()
    {
        $this->dataLayerVariables['affiliate'] = $this->cookieAffiliateCode;

        if ($this->affiliateDataLayer) {
            $this->mergeIntoDataLayer($this->affiliateDataLayer);
        }
    }

    private function getLastOrder()
    {
        $orderId = $this->checkoutSession->getData('last_order_id');
        return $this->orderRepository->get($orderId);
    }

    private function checkForValidAffiliateAndInitAffiliateVariables()
    {
        $affiliateURLList = $this->getAffiliateURLParamList();

        if (isset($affiliateURLList[$this->cookieAffiliateCode])) {
            $this->initAffiliateDataLayerViaLocator($affiliateURLList[$this->cookieAffiliateCode]);
        }
    }

    /**
     * @param string $affiliate
     */
    private function initAffiliateDataLayerViaLocator(string $affiliate)
    {
        if ($affiliate && $this->affiliateHelperLocator->isValid($affiliate)) {
            $affiliateHelper = $this->affiliateHelperLocator->locate($affiliate);
            $this->affiliateDataLayer = $affiliateHelper->getAffiliateDataLayer();
        }
    }

    /**
     * @param array $mergeRequest
     */
    private function mergeIntoDataLayer($mergeRequest)
    {
        $this->dataLayerVariables = array_merge($mergeRequest, $this->dataLayerVariables);
    }

    private function ukNumberFormat($number)
    {
        if (is_numeric($number)) {
            return number_format($number, 2, '.', '');
        }
        return '';
    }

    private function getAffiliateCodeFromCookie()
    {
        return $this->trackingCookieHelper->getAffiliateCookie();
    }
}