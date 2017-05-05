<?php

namespace Limitless\GlobalListerBanner\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class View extends Template
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;


    public function __construct(Template\Context $context, array $data = [])
    {
        $this->scopeConfig = $context->getScopeConfig();
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    public function buildListerBannerHtml()
    {

        $altText = $this->getBannerAltText();
        $bannerLink = $this->getBannerUrl();
        $desktopBanner = $this->getDesktopListerBanner();
        $mobileBanner = $this->getMobileListerBanner();

        if (empty($bannerLink)) {
            $desktopBannerHtml = '<img class="desktop" src="' . $desktopBanner . '" alt="' . $altText . '" />';
            $mobileBannerHtml = '<img class="mobile " src="' . $mobileBanner . '" alt="' . $altText . '" />';
        } else {
            $desktopBannerHtml = '<a class="desktop "href="' . $bannerLink . '"><img src="' . $desktopBanner .
                '" alt="' . $altText . '" /></a>';
            $mobileBannerHtml = '<a class="mobile" href="' . $bannerLink . '"><img src="' . $mobileBanner .
                '" alt="' . $altText . '" /></a>';
        }

        return $desktopBannerHtml . $mobileBannerHtml;

    }

    private function getConfig($path)
    {
        return $this->scopeConfig->getValue(
            'general/limitless_global_lister_banner/' . $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    private function getBannerUrl()
    {
        return $this->getConfig('banner_url');
    }

    private function getBannerAltText()
    {
        return $this->getConfig('banner_alt_text');
    }

    private function getDesktopListerBanner()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'global_lister_banner/' . $this->getConfig('desktop_banner_image');
    }

    private function getMobileListerBanner()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'global_lister_banner/' . $this->getConfig('mobile_banner_image');
    }

}