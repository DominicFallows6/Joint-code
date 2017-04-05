<?php

namespace Limitless\TagManagerDataLayer\Helper\AffiliatesDataLayer;

use Limitless\TagManagerDataLayer\Api\AffiliateHelperInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\View\Element\Template\Context;

class WebgainsDataLayer implements AffiliateHelperInterface
{
    const WEBGAINS_CONFIGPATH = 'google/limitless_tagmanager_datalayer/affiliate_tracking/webgains/';

    /** @var Session */
    protected $checkoutSession;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var OrderInterface */
    private $lastOrder;

    public function __construct(
        Session $checkoutSession,
        OrderRepositoryInterface $orderRepository,
        Context $context
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * @return array
     */
    public function getAffiliateDataLayer(): array
    {
        $this->initLastOrder();

        return array(
            'wgOrderValue' => $this->getWebgainsOrderValue(),
            'wgItems' => $this->getWebgainsProductString($this->lastOrder->getCouponCode()),
            'wgComment' => $this->getWebgainsComment()
        );
    }

    private function initLastOrder()
    {
        $orderId = $this->checkoutSession->getData('last_order_id');
        $this->lastOrder =  $this->orderRepository->get($orderId);
    }

    private function getWebgainsOrderValue()
    {
        $vatSetting = $this->getWebgainsVATSetting();
        $shippingSetting = $this->getWebgainsShippingSetting();
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

    private function getWebgainsProductString($voucherCode = '')
    {
        $orderItems = $this->lastOrder->getItems();

        $wgDelimiter = '::';
        $wgLineDelimiter = '|';
        $wgProductStrings = [];

        $trackingEventId = $this->getWebgainsTrackingEventId();
        $orderCouponCode = $voucherCode;

        foreach ($orderItems as $orderItem)
        {
            $wgProductStrings[] = implode(
                $wgDelimiter,
                [
                    $trackingEventId,
                    $this->getWebgainsItemPrice($orderItem),
                    $orderItem->getName(),
                    $orderItem->getSku(),
                    $orderCouponCode
                ]
            );
        }
        $wgProductString = implode($wgLineDelimiter, $wgProductStrings);

        return $wgProductString;
    }

    private function getWebgainsItemPrice($orderItem)
    {
        $vatSetting = $this->getWebgainsVATSetting();

        switch ($vatSetting) {
            case 'exclude':
                $productPrice = $this->ukNumberFormat($orderItem->getPriceInclTax() -
                    ($orderItem->getTaxAmount() / $orderItem->getQtyOrdered()));
                break;
            case 'include';
            default:
                $productPrice = $this->ukNumberFormat($orderItem->getPriceInclTax());
            break;
        }
        return $productPrice;
    }

    private function ukNumberFormat($number)
    {
        return number_format($number, 2);
    }

    private function getWebgainsConfigSettings($setting)
    {
        return $this->scopeConfig->getValue(
            self::WEBGAINS_CONFIGPATH . $setting,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    private function getWebgainsTrackingEventId()
    {
        return (int) $this->getWebgainsConfigSettings('webgains_tracking_event_id') ?? 0;
    }

    private function getWebgainsComment()
    {
        return $this->getWebgainsConfigSettings('webgains_comment') ?? '';
    }

    private function getWebgainsVATSetting()
    {
        return $this->getWebgainsConfigSettings('webgains_total_vat_setting') ?? 'include';
    }

    private function getWebgainsShippingSetting()
    {
        return $this->getWebgainsConfigSettings('webgains_total_shipping_setting') ?? 'include';
    }
}