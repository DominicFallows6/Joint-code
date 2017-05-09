<?php

namespace Limitless\TrustpilotEmail\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Details extends Template
{
    /** @var array */
    private $localeMapping;

    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->buildLocales();
    }

    private function buildLocales()
    {
        $this->localeMapping = [
            'US' => 'en-US',
            'GB' => 'en-GB'
        ];
    }

    public function getLocaleFromCountry($countryCode)
    {
        if (isset($this->localeMapping[$countryCode])) {
            return $this->localeMapping[$countryCode];
        } else {
            //Generate in format "countrycode-COUNTRYCODE"
           return strtolower($countryCode) . '-' . strtoupper($countryCode);
        }
    }

    public function getTrustpilotOrderItemsString($order)
    {
        $tpItemString = '';

        /** @var \Magento\Sales\Api\Data\OrderItemInterface[] $orderItems */
        $orderItems = $order->getItems();

        /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
        foreach ($orderItems as $item)
        {
            $tpItemString .= '{';
            $itemProduct = $item->getProduct();

            $itemCode = $item->getSku();
            if (!empty($itemProduct->getData('alias'))){
                $itemCode = $itemProduct->getData('alias');
            }

            $tpItemString .= '"sku": "' . $this->escapeHtml($itemCode) . '",';
            $tpItemString .= '"name": "' . $this->escapeHtml($item->getName()) . '",';
            $tpItemString .= '"productUrl": "' . $this->escapeHtml($itemProduct->getProductUrl()) . '",';

            //Image - TODO append image site link?
            //getImage / getSmallImage / getThumbnail
            $imageUrl =
                $itemProduct->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                . 'catalog/product'
                . $itemProduct->getThumbnail();
            $tpItemString .= '"imageUrl": "' . $this->escapeHtml($imageUrl) . '"';

            $tpItemString .= '},';
        }

        $tpItemString = rtrim($tpItemString, ',');

        return $tpItemString;
    }
}