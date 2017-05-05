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

        if($base_admin_placeholder == "") {
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

    public function getConfig($path) {
        return $this->scopeConfig->getValue('general/limitless_featured_categories/' . $path, ScopeInterface::SCOPE_STORE);
    }

    public function getFeaturedImageUrl($path) {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'featured/' . $this->getConfig($path);
    }

    public function getMobileFeaturedImageUrl() {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'featured/' . $this->getConfig('featured_five_image_mobile');
    }

    public function getMobileFeaturedImageHtml() {
        if(substr($this->getMobileFeaturedImageUrl(), -1) !== '/') {
            return '<img class="mobile-featured" src="' . $this->getMobileFeaturedImageUrl() . '" />';
        } else {
            return  '<img class="mobile-featured" src="' . $this->getProductImagePlaceholder() . '" />';
        }
    }

    public function getFeaturedCategories()
    {
        $featured_html = "";
        $featured_html_right = "";

        foreach ($this->getFeaturedConfig() as $key => $value) {
            if($key === 4) {
                $image = $this->getFeaturedImageHtml($value) .  $this->getMobileFeaturedImageHtml();
                $featured_category = $this->buildFeaturedCategoryHtml($value, $image);
                $featured_html_right .= $featured_category;
            } else {
                $image = $this->getFeaturedImageHtml($value);
                $featured_category = $this->buildFeaturedCategoryHtml($value, $image);
                $featured_html .= $featured_category;
            }
        }

        return '<div class="featured-left">' . $featured_html . "</div>" . '<div class="featured-right">' . $featured_html_right . "</div>";
    }

    private function buildFeaturedCategoryHtml($value, $image)
    {

        if($value['featured_category'] != "") {
            $featured_title = "active-title";
        } else {
            $featured_title="no-title";
        }

        $callToAction = __('Zu den Produkten');
        $featured_category = '<a href="' . $value['featured_category_url'] . '">' . $image . '<div class="featured-text ' . $featured_title . '"><div><span>' . $value['featured_category'] . '</span><span class="cta">' . $callToAction . '</span></div></div></a>';

        return $featured_category;
    }

    public function getFeaturedConfig()
    {

       return [
            [
                "featured_category" => $this->getConfig('featured_one_title'),
                "featured_category_url" => $this->getConfig('featured_one_url'),
                "featured_category_image" => $this->getFeaturedImageUrl("featured_one_image"),
            ],
            [
                "featured_category" => $this->getConfig('featured_two_title'),
                "featured_category_url" => $this->getConfig('featured_two_url'),
                "featured_category_image" => $this->getFeaturedImageUrl("featured_two_image"),
            ],
            [
               "featured_category" => $this->getConfig('featured_three_title'),
               "featured_category_url" => $this->getConfig('featured_three_url'),
               "featured_category_image" => $this->getFeaturedImageUrl("featured_three_image"),
            ],
            [
               "featured_category" => $this->getConfig('featured_four_title'),
               "featured_category_url" => $this->getConfig('featured_four_url'),
               "featured_category_image" => $this->getFeaturedImageUrl("featured_four_image"),
            ],
            [
               "featured_category" => $this->getConfig('featured_five_title'),
               "featured_category_url" => $this->getConfig('featured_five_url'),
               "featured_category_image" => $this->getFeaturedImageUrl("featured_five_image"),
            ]
        ];

    }
}