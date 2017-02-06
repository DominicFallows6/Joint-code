<?php

namespace Limitless\HomepageBanner\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class View extends Template
{
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
        $mediaDir = 'media/homepage_banners/';
        return $this->getBaseUrl() . $mediaDir . $this->getDesktopBannerImageConfig();
    }
    public function getMobileHomepageBanner()
    {
        $mediaDir = 'media/homepage_banners/';
        return $this->getBaseUrl() . $mediaDir . $this->getMobileBannerImageConfig();
    }
    public function getBannerLink()
    {
        return $this->getBaseUrl() . $this->getConfig('limitless_homepage_banner_link');
    }
}