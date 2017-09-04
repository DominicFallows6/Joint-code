<?php

namespace Limitless\PaypalStaticImage\Plugin;

use Magento\Backend\Block\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Paypal\Model\Config;

class UseStaticPaypalImage
{
    /**
     * @var Template
     */
    private $template;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(Template $template, ScopeConfigInterface $scopeConfig)
    {
        $this->template = $template;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Either use local static image for Paypal or use Paypal to create image
     *
     * @param Config $subject
     * @param \Closure $proceed
     * @param string $localeCode
     * @param float|null $orderTotal
     * @param string|null $pal encrypted summary about merchant
     * @return string
     */
    public function aroundGetExpressCheckoutShortcutImageUrl(
        Config $subject,
        \Closure $proceed,
        $localeCode,
        $orderTotal = null,
        $pal = null
    ) {
        if ($this->getStaticPaypalImageEnabled()) {
            return $this->getStaticImage();
        } else {
            return $proceed($localeCode, $orderTotal, $pal);
        }
    }

    private function getStaticImage()
    {
        return $this->template->getViewFileUrl('Limitless_PaypalStaticImage::images/paypalstatic.gif');
    }

    private function getStaticPaypalImageEnabled()
    {
        return $this->scopeConfig->getValue(
            'payment/paypal_static_image/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}