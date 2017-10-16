<?php

namespace Limitless\Seo\Model;

use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

class RobotsExclusions
{
    protected $urlParamsToExcludeRawString;
    protected $urlParamsToExclude;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        Context $context
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->processExclusions();
    }

    public function shouldAssetBeNoFollow(array $parameters): bool
    {
        $result = false;

        if (!empty($this->urlParamsToExclude)) {
            foreach ($parameters as $exclusionKey => $exclusionValue) {
                if (in_array($exclusionKey, $this->urlParamsToExclude)) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * This is run this way as the exclusion array may be checked multiple times in one request
     */
    private function processExclusions()
    {
        $this->getRobotExclusions();
        $this->convertExclusionsFromListToArray();
    }

    private function convertExclusionsFromListToArray()
    {
        $result = explode(',', $this->urlParamsToExcludeRawString);
        $this->urlParamsToExclude = array_map('trim', $result);
    }

    private function getRobotExclusions()
    {
        $scopeConfigPath = 'web/limitless_robot_exclusions/robot_exclusions';
        $this->urlParamsToExcludeRawString = $this->scopeConfig->getValue($scopeConfigPath, ScopeInterface::SCOPE_STORE);
    }

}