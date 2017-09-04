<?php

namespace Limitless\PaypalStaticImage\Plugin;

use Magento\Backend\Block\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Asset\Repository;
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
    /**
     * @var Repository
     */
    private $assetRepository;


    public function __construct(Template $template, ScopeConfigInterface $scopeConfig, Repository $repository)
    {
        $this->template = $template;
        $this->scopeConfig = $scopeConfig;
        $this->assetRepository = $repository;
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
        $payPalImageViewSource = null;

        if ($this->getStaticPaypalImageEnabled()) {
            $payPalImageViewSource = $this->getStaticImage($localeCode);
        }

        if (null == $payPalImageViewSource) {
            $payPalImageViewSource = $proceed($localeCode, $orderTotal, $pal);
        }

        return $payPalImageViewSource;
    }

    private function getStaticImage($locale)
    {
        $fileSource = 'Limitless_PaypalStaticImage::images/'.$locale.'/paypalstatic.gif';
        $viewSource = null;
        if ($this->paypalImageExists($fileSource))
        {
            $viewSource = $this->template->getViewFileUrl($fileSource);
        }

        return $viewSource;
    }

    private function paypalImageExists($source)
    {
        $params = [
            'area' => 'frontend'
        ];
        $asset = $this->assetRepository->createAsset($source, $params);
        try {
            $asset->getSourceFile();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getStaticPaypalImageEnabled()
    {
        return $this->scopeConfig->getValue(
            'payment/paypal_static_image/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}