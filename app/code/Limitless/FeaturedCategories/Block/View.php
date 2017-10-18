<?php

namespace Limitless\FeaturedCategories\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;

class View extends Template
{

    /**
     * @var ScopeConfigInterface
     * */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * View constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->scopeConfig = $context->getScopeConfig();
        $this->storeManager = $context->getStoreManager();

    }

    private function getProductImagePlaceholder()
    {
        $base_admin_placeholder = $this->scopeConfig->getValue('catalog/placeholder/image_placeholder');
        $placeholder = $this->getViewFileUrl('Limitless_FeaturedCategories::images/placeholder.jpg');

        if ($base_admin_placeholder == "") {
            return $placeholder;
        } else {
            return '/media/catalog/product/placeholder/' . $base_admin_placeholder;
        }
    }

    private function getFeaturedImageHtml($value) {
        if(substr($value['featured_category_image'], -1) !== '/') {
            return '<img src="' . $value['featured_category_image'] . '" />';
        } else {
            return  '<img src="' . $this->getProductImagePlaceholder() . '" />';
        }
    }

    private function getFeaturedSideImageHtml($value) {

        if (substr($value['featured_category_image'], -1) !== '/') {
            $desktopFeaturedSideImageUrl = $value['featured_category_image'];
        } else {
            $desktopFeaturedSideImageUrl = $this->getProductImagePlaceholder();
        }

        if (substr($this->getMobileFeaturedImageUrl(), -1) !== '/') {
            $mobileFeaturedSideImageUrl = $this->getMobileFeaturedImageUrl();
        } else {
            $mobileFeaturedSideImageUrl = $this->getProductImagePlaceholder();
        }

        $featuredSideImageHtml = '<picture>' .
            '<source media="(max-width: 767px)" srcset="' . $mobileFeaturedSideImageUrl . '">' .
            '<img src="' . $desktopFeaturedSideImageUrl .'" />' .
            '</picture>';

        return $featuredSideImageHtml;
    }

    public function getConfig($path) {
        return $this->scopeConfig->getValue('general/limitless_featured_categories/' . $path, ScopeInterface::SCOPE_STORE);
    }

    public function getLayoutConfig() {
        return $this->getConfig('featured_layout');
    }

    public function getCtaText() {
        return $this->getConfig('featured_cta_text');
    }

    public function getFeaturedImageUrl($path) {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'featured/' . $this->getConfig($path);
    }

    public function getMobileFeaturedImageUrl() {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'featured/' . $this->getConfig('featured_five_image_mobile');
    }

    public function getFeaturedCategories()
    {
        $featured_html = "";
        $featured_html_side = "";

        foreach ($this->getFeaturedConfig() as $key => $value) {
            if ($key === 4) {
                $image = $this->getFeaturedSideImageHtml($value);
                $featured_category = $this->buildFeaturedCategoryHtml($value, $image);
                $featured_html_side .= $featured_category;
            } else {
                $image = $this->getFeaturedImageHtml($value);
                $featured_category = $this->buildFeaturedCategoryHtml($value, $image);
                $featured_html .= $featured_category;
            }
        }

        if ($this->getLayoutConfig() == 'one_column') {
            return '<div class="featured">' . $featured_html . "</div>";
        } else {
            return '<div class="featured">' .  $featured_html . "</div>" . '<div class="featured-side">' . $featured_html_side . '</div>';
        }

    }

    private function buildFeaturedCategoryHtml($value, $image)
    {

        $callToAction = $this->getCtaText();

        if ($value['featured_sub_title'] != "") {
            $featured_sub_title = '<span class="sub-title">' . $value['featured_sub_title'] . '</span>';
        } else {
            $featured_sub_title = "";
        }

        if ($callToAction !='') {
            $featured_cta_active = "cta-active";
            $cta_html = '<span class="cta action primary">' . $callToAction . '</span>';
        } else {
            $cta_html = "";
            $featured_cta_active = "";
        }

        if ($value['featured_category'] != "") {
            $featured_title = '<div class="featured-text '.$featured_cta_active.'">' .
                '<div>' .
                    '<span class="title">' .
                        $value['featured_category'] .
                    '</span>' .
                        $featured_sub_title .
                '</div>' .
            '</div>' . $cta_html;
        } else {
            $featured_title = "";
        }


        if ($value['featured_enabled'] == true) {
            $featured_category = '<div class="featured-category">' .
                '<a href="' . $value['featured_category_url'] . '">' . $image . $featured_title.'</a>' .
            '</div>';
        } else {
            $featured_category = "";
        }

        return $featured_category;
    }

    public function getFeaturedConfig()
    {

        return [
            [
                "featured_enabled" => $this->getConfig('featured_one_enabled'),
                "featured_category" => $this->getConfig('featured_one_title'),
                "featured_sub_title" => $this->getConfig('featured_one_sub_title'),
                "featured_category_url" => $this->getConfig('featured_one_url'),
                "featured_category_image" => $this->getFeaturedImageUrl("featured_one_image"),
            ],
            [
                "featured_enabled" => $this->getConfig('featured_two_enabled'),
                "featured_category" => $this->getConfig('featured_two_title'),
                "featured_sub_title" => $this->getConfig('featured_two_sub_title'),
                "featured_category_url" => $this->getConfig('featured_two_url'),
                "featured_category_image" => $this->getFeaturedImageUrl("featured_two_image"),
            ],
            [
                "featured_enabled" => $this->getConfig('featured_three_enabled'),
                "featured_category" => $this->getConfig('featured_three_title'),
                "featured_sub_title" => $this->getConfig('featured_three_sub_title'),
                "featured_category_url" => $this->getConfig('featured_three_url'),
                "featured_category_image" => $this->getFeaturedImageUrl("featured_three_image"),
            ],
            [
                "featured_enabled" => $this->getConfig('featured_four_enabled'),
                "featured_category" => $this->getConfig('featured_four_title'),
                "featured_sub_title" => $this->getConfig('featured_four_sub_title'),
                "featured_category_url" => $this->getConfig('featured_four_url'),
                "featured_category_image" => $this->getFeaturedImageUrl("featured_four_image"),
            ],
            [
                "featured_enabled" => $this->getConfig('featured_five_enabled'),
                "featured_category" => $this->getConfig('featured_five_title'),
                "featured_sub_title" => $this->getConfig('featured_five_sub_title'),
                "featured_category_url" => $this->getConfig('featured_five_url'),
                "featured_category_image" => $this->getFeaturedImageUrl("featured_five_image"),
            ]
        ];

    }
}