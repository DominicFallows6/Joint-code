<?php

namespace Limitless\Delivery\Plugin;

use Magento\Quote\Model\Cart\ShippingMethodConverter;
use Magento\Tax\Helper\Data;

class RemoveShippingTaxFromDatepickerPlugin
{
    /**
     * @var Data
     */
    private $taxHelper;

    public function __construct(Data $taxHelper)
    {
        $this->taxHelper = $taxHelper;
    }
    /**
     * @param ShippingMethodConverter $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote\Address\Rate $rateModel The rate model.
     * @param $quoteCurrencyCode
     * @return $this|mixed
     */
    public function aroundModelToDataObject(
        ShippingMethodConverter $subject,
        \Closure $proceed,
        $rateModel,
        $quoteCurrencyCode
    ) {

        $inclTax = ($rateModel->getData('vat_id') == '') ? true : false;
        $price = $this->getShippingPriceWithFlag($rateModel, $inclTax);
        $rateModel->setPrice($price);

        return $proceed($rateModel, $quoteCurrencyCode);
    }

    /**
     * Get Shipping Price including or excluding tax
     *
     * @param \Magento\Quote\Model\Quote\Address\Rate $rateModel
     * @param bool $flag
     * @return float
     */
    private function getShippingPriceWithFlag($rateModel, $flag)
    {
        return $this->taxHelper->getShippingPrice(
            $rateModel->getPrice(),
            $flag,
            $rateModel->getAddress(),
            $rateModel->getAddress()->getQuote()->getCustomerTaxClassId()
        );
    }

}
