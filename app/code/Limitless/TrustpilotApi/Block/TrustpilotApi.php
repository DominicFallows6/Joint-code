<?php

namespace Limitless\TrustpilotApi\Block;

use Limitless\TrustpilotApi\Model\TrustpilotCacheFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;

class TrustpilotApi extends Template
{
    //TODO may need product page widget adding (design needed)

    /** @var TrustpilotCacheFactory */
    private $trustpilotCacheFactory;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(
        Context $context,
        TrustpilotCacheFactory $trustpilotCacheFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->trustpilotCacheFactory = $trustpilotCacheFactory;
        $this->storeManager = $context->getStoreManager();
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * @param string $path
     * @param int|null $siteCode
     * @return mixed
     */
    private function getTrustPilotApiConfigValue($path, $siteCode = null)
    {
        return $this->scopeConfig->getValue(
            'general/trustpilot/trustpilot_api/' . $path . '',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $siteCode
        );
    }

    /**
     * @param int|null $siteCode
     * @return int
     */
    public function getTrustpilotApiEnabled($siteCode = null): int
    {
        return (int) ($this->getTrustPilotApiConfigValue('enabled', $siteCode) ?? 0);
    }

    /**
     * @param int|null $siteCode
     * @return string
     */
    public function getTrustpilotApiMode($siteCode = null): string
    {
        //ajax or cache
        return (string) ($this->getTrustPilotApiConfigValue('api_mode', $siteCode) ?? 'ajax');
    }

    /**
     * @param int|null $siteCode
     * @return string
     */
    public function getTrustpilotApiKey($siteCode = null): string
    {
        return (string) ($this->getTrustPilotApiConfigValue('api_key', $siteCode) ?? '');
    }

    /**
     * @param int|null $siteCode
     * @return string
     */
    public function getTrustpilotApiSecret($siteCode = null): string
    {
        return (string) ($this->getTrustPilotApiConfigValue('api_secret', $siteCode) ?? '');
    }

    /**
     * @param int|null $siteCode
     * @return string
     */
    public function getTrustpilotBusinessId($siteCode = null): string
    {
        return (string) ($this->getTrustPilotApiConfigValue('business_id', $siteCode) ?? '');
    }

    /**
     * @return string
     */
    public function getTrustpilotUrl(): string
    {
        return (string) $this->getTrustPilotApiConfigValue('trustpilot_url') ?? '';
    }

    /**
     * @return string
     */
    public function getTrustpilotSiteName(): string
    {
        return (string) $this->getTrustPilotApiConfigValue('trustpilot_sitename') ?? '';
    }

    /**
     * @return string
     */
    public function getTrustpilotLabels(): string
    {
        return (string) $this->getTrustPilotApiConfigValue('trust_score_labels') ?? '';
    }

    /**
     * @param int|null $siteCode
     * @return string
     */
    public function getTrustpilotStarRatings($siteCode = null): string
    {
        return (string) $this->getTrustPilotApiConfigValue('trust_star_ratings', $siteCode) ?? '';
    }

    /**
     * @param int|null $siteCode
     * @return string
     */
    private function getTrustpilotRestBusiness($siteCode = null): string
    {
        return (string) ($this->getTrustPilotApiConfigValue('trust_rest_business', $siteCode) ?? '');
    }

    /**
     * @param int|null $siteCode
     * @return string
     */
    private function getTrustpilotRestReviews($siteCode = null): string
    {
        return $this->getTrustPilotApiConfigValue('trust_rest_reviews', $siteCode) ?? '';
    }

    /**
     * @param int|null $siteCode
     * @return int
     */
    public function getTrustpilotReviewsCount($siteCode = null): int
    {
        $perPage = (int) $this->getTrustPilotApiConfigValue('trust_reviews_count', $siteCode);
        if ($perPage < 0 || $perPage > 25)
        {
            $perPage = 12;
        }
        return $perPage;
    }

    /**
     * @param int|null $siteCode
     * @return string
     */
    public function getTrustpilotApiBusinessURLClean($siteCode = null): string
    {
        return (string) $this->cleanTrustpilotUrl($this->getTrustpilotRestBusiness($siteCode), $siteCode);
    }

    /**
     * @param int|null $siteCode
     * @return string
     */
    public function getTrustpilotApiReviewsURLClean($siteCode = null): string
    {
        return (string) $this->cleanTrustpilotUrl($this->getTrustpilotRestReviews($siteCode), $siteCode);
    }

    /**
     * @param string $url
     * @param int|null $siteCode
     * @return string
     */
    private function cleanTrustpilotUrl(string $url, $siteCode = null): string
    {
        if (strpos($url, '{business_id}') !== false) {
            $url = str_replace('{business_id}', $this->getTrustpilotBusinessId($siteCode), $url);
        }

        $url = $this->cleanTrustpilotUrlParams($url, $siteCode);

        return $url;
    }

    /**
     * @param string $url
     * @param int|null $siteCode
     * @return string
     */
    private function cleanTrustpilotUrlParams(string $url, $siteCode = null): string
    {
        $params = [];

        if (strpos($url, '{stars}') !== false) {
            $stars = $this->getTrustpilotStarRatings($siteCode);

            $params[] = 'stars='.$stars;
            $url = str_replace('{stars}', '', $url);
        }

        if (strpos($url, '{per_page}') !== false) {
            $perPage = (int) $this->getTrustpilotReviewsCount($siteCode);
            $params[] = 'perPage='.$perPage;
            $url = str_replace('{per_page}', '', $url);
        }

        $paramsString = implode('&', $params);
        return $url . $paramsString;
    }

    /**
     * @return string
     */
    public function getTrustpilotUrlLogo(): string
    {
        $trustpilotUrlOpen = '<a href="'. $this->getTrustpilotUrl() . '/review/' . $this->getTrustpilotSiteName() .'" target="_blank">';
        $trustpilotUrlClose = '</a>';
        $trustpilotLogoHtml = '<img src="'.$this->getViewFileUrl('Limitless_TrustpilotApi::images/trustpilot-logo.svg').'" alt="Trustpilot" />';
        $trustpilotLinkLabel = '<span>'. __('Powered by') . '</span>';

        if ($this->getTrustpilotUrl() != "" && $this->getTrustpilotSiteName() != "") {
            return $trustpilotUrlOpen . $trustpilotLinkLabel . $trustpilotLogoHtml . $trustpilotUrlClose;
        } else {
            return $trustpilotLinkLabel . $trustpilotLogoHtml;
        }
    }

    /**
     * @param string $cacheType
     * @return string
     */
    public function getCachedData($cacheType = 'business'): string
    {
        $cachedJSString = '';

        if (strcasecmp($this->getTrustpilotApiMode(), 'cache') === 0)
        {
            $currentSiteId = $this->storeManager->getStore()->getId();
            $trustpilotCache = $this->trustpilotCacheFactory->create();

            try {
                $trustpilotCache->getResource()->load($trustpilotCache, $currentSiteId, $trustpilotCache::STORE_CODE);

                switch(strtolower($cacheType))
                {
                    case 'review':
                        $cachedJSString = $trustpilotCache->getData($trustpilotCache::REVIEW_CACHE) ?? '';
                        break;
                    case 'business':
                    default:
                        $cachedJSString = $trustpilotCache->getData($trustpilotCache::BUSINESS_UNITS_CACHE) ?? '';
                        break;
                }
            } catch (\Exception $e) {
                return '';
            }
        }
        return $this->cleanJsEscapeChars($cachedJSString);
    }

    private function cleanJsEscapeChars(string $jsString): string
    {
        //1 - clean ampersands
        //2 - clean new lines
        //3 - clean escape quotes
        //4 - replace quotes

        $jsString = str_replace ('&', '&amp;', $jsString);
        $jsString = str_replace ('\n', '', $jsString);
        $jsString = str_replace ('\"', '\'', $jsString);
        $jsString = $this->escapeQuote($jsString);

        return $this->escapeJsQuote($jsString);
    }
}