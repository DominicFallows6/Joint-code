<?php

namespace Limitless\HomepageBanner\Block;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class View extends Template
{

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(Template\Context $context, array $data = [])
    {
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    public function getConfig($path)
    {
        return $this->_scopeConfig->getValue('general/limitless_homepage_banner/' . $path,
            ScopeInterface::SCOPE_STORE);
    }

    public function getDesktopBannerImageConfig()
    {
        return $this->getConfig('limitless_homepage_banner_image');
    }

    public function getMobileBannerImageConfig()
    {
        return $this->getConfig('limitless_mobile_homepage_banner_image');
    }

    public function getImgAltText()
    {
        return $this->getConfig('limitless_homepage_banner_alt');
    }

    public function getBackgroundColour()
    {
        $bkgcolour = $this->getConfig('limitless_banner_background_colour');
        if (!$bkgcolour == '') {
            return ' background-color:' . $bkgcolour . ';';
        } else {
            return '';
        }
    }

    public function getDesktopHomepageBanner()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'homepage_banners/' . $this->getDesktopBannerImageConfig();
    }

    public function getMobileHomepageBanner()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'homepage_banners/' . $this->getMobileBannerImageConfig();
    }

    public function getBannerLink()
    {
        return $this->getConfig('limitless_homepage_banner_link');
    }

    public function getBanners()
    {
        $mobileHomepageBanner =  $this->getMobileHomepageBanner();
        $desktopHomepageBanner = $this->getDesktopHomepageBanner();
        $altText = $this->getImgAltText();

        $homePageBanner = '<picture>' .
                    '<source media="(max-width: 480px)" srcset="' . $mobileHomepageBanner . '">' .
                    '<img class="hp-banner" src="' . $desktopHomepageBanner . '" alt="' . $altText . '"/>' .
                '</picture>';

        return $homePageBanner;
    }

    public function getBannerWithLinkOrWithoutLink()
    {
        if ($this->getBannerLink()) {
            return '<a href="' . $this->getBannerLink() . '">' . $this->getBanners() . '</a>';
        } else {
            return $this->getBanners();
        }
    }
}