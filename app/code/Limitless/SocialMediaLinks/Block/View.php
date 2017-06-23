<?php

namespace Limitless\SocialMediaLinks\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class View extends Template
{

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        Context $context,
        array $data = []
    )
    {
        parent::__construct($context,$data);
        $this->scopeConfig = $context->getScopeConfig();
    }

    public function getSocialMediaLinks() {
        $social = "";
        foreach($this->getSocialConfig() as $key => $value) {
            $social .= $this->getLinkHtml($value);
        }
        return '<div class="social-media-links '. $this->getSocialIconColorConfig() .'">' . $this->getSocialMediaHdr() . $social . '</div>';
    }

    private function getLinkHtml($value) {

        $link = $value['social_link'];
        $class = $value['social_class'];

        if($this->getLabelVisibleConfig() === "1") {
            $title = '<span style="'.$this->getIconLabelLineHeight().'">' . $value['social_title'] . '</span>';
        } else {
            $title = '';
        }

        if($link != "") {
            $socialLinkHtml = '<a target="_blank" class="'.$class.'" href="'.$link.'"><div class="social-icon" style="'.$this->getIconBackgroundConfig(). $this->getIconBorderConfig() . $this->getIconHeight() .'"><span class="icon"></span></div>' . $title . '</a>';
        } else {
            $socialLinkHtml = '';
        }

        return $socialLinkHtml;
    }

    private function getSocialMediaHdr() {
        $socialHdr = $this->getScopeConfigValue('social_header');
        if($socialHdr != "") {
            return '<span class="header">' . $socialHdr .'</span>';
        } else {
            return '';
        }
    }

    private function getScopeConfigValue($path)
    {
        return $this->scopeConfig->getValue('general/limitless_social_media/' . $path, ScopeInterface::SCOPE_STORE);
    }

    private function getLabelVisibleConfig() {
        $titleVisibleValue = $this->getScopeConfigValue('show_label');
        return $titleVisibleValue;
    }

    private function getSocialIconColorConfig() {
        $socialIconColorValue = $this->getScopeConfigValue('social_theme');
        return $socialIconColorValue;
    }

    private function getIconBackgroundConfig() {
        $socialIconBg = $this->getScopeConfigValue('custom_background');
        if($socialIconBg != "") {
            return 'background-color:'. $socialIconBg .';';
        } else {
            return '';
        }
    }

    private function getIconBorderConfig() {
        $socialIconBorder = $this->getScopeConfigValue('icon_border_colour');
        if($socialIconBorder != "") {
            return 'border:2px solid '. $socialIconBorder .';';
        } else {
            return '';
        }
    }

    private function getIconHeight() {
        $iconHeightandWidth = $this->getScopeConfigValue('icon_height');
        if($iconHeightandWidth != "") {
            return 'width:'.$iconHeightandWidth.';height:'.$iconHeightandWidth.';';
        } else {
            return '';
        }
    }

    private function getIconLabelLineHeight() {
        $iconLineHeight = $this->getScopeConfigValue('icon_height');
        if($iconLineHeight != "") {
            return 'line-height:'.$iconLineHeight.';';
        } else {
            return '';
        }
    }

    private function getSocialConfig():array
    {
        return [
            [
                "social_link" => $this->getScopeConfigValue('facebook_link'),
                "social_title" => "Facebook",
                "social_class" => "facebook"
            ],
            [
                "social_link" => $this->getScopeConfigValue('twitter_link'),
                "social_title" => "Twitter",
                "social_class" => "twitter"
            ],
            [
                "social_link" => $this->getScopeConfigValue('pinterest_link'),
                "social_title" => "Pinterest",
                "social_class" => "pinterest"
            ],
            [
                "social_link" => $this->getScopeConfigValue('google_plus_link'),
                "social_title" => "Google +",
                "social_class" => "google-plus"
            ],
            [
                "social_link" => $this->getScopeConfigValue('youtube_link'),
                "social_title" => "YouTube",
                "social_class" => "you-tube"
            ],
            [
                "social_link" => $this->getScopeConfigValue('blog_link'),
                "social_title" => "Blog",
                "social_class" => "blog"
            ],
            [
                "social_link" => $this->getScopeConfigValue('houzz_link'),
                "social_title" => "Houzz",
                "social_class" => "houzz"
            ],
            [
                "social_link" => $this->getScopeConfigValue('instagram_link'),
                "social_title" => "Instagram",
                "social_class" => "instagram"
            ]
        ];
    }
}