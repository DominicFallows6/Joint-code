<?php

namespace Limitless\TagManagerDataLayer\Api;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

abstract class DataLayerAbstract extends Template
{
    const DATALAYER_CONFIGPATH = 'google/limitless_tagmanager_datalayer/';

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->setTemplate('content/datalayer.phtml');
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * @return string
     */
    public function getDataLayerHtml(): string
    {
        $this->initDataLayerVariables();

        $staticContentString = '<script type="text/javascript">';
        $staticContentString .= 'dataLayer.push({';
        $staticContentString .= $this->getDataLayerName() . ' :{ '.$this->getDataLayerVariablesFormattedForJavascript($this->getDataLayerVariables()).'}';
        $staticContentString .= '})';
        $staticContentString .= '</script>';

        return $staticContentString;
    }

    /**
     * @param mixed[] $dataLayerVariables
     * @return string
     */
    private function getDataLayerVariablesFormattedForJavascript(array $dataLayerVariables): string
    {
        $dataLayerString = '';

        foreach ($dataLayerVariables as $dataLayerKey => $dataLayerValue) {
            if (is_array($dataLayerValue)) {
                $dataLayerString .= "'" . $dataLayerKey . "':{" . $this->getDataLayerVariablesFormattedForJavascript($dataLayerValue) . "},";
            } else if (is_numeric($dataLayerValue) ||
                (substr($dataLayerValue, 0, 1) === '[' &&  substr($dataLayerValue, -1, 1) === ']')) {
                $dataLayerString .= "'" . $dataLayerKey . "':" . $dataLayerValue . ",";
            } else if (substr($dataLayerValue, 0, 1) === '\\') {
                //If starts with \ make it always a string but remove \ (see leGuide)
                $dataLayerString .= "'" . $dataLayerKey . "':'" . substr($dataLayerValue, 1) . "',";
            } else {
                $dataLayerString .= "'" . $dataLayerKey . "':'" . $dataLayerValue . "',";
            }
        }

        return rtrim($dataLayerString, ',');
    }

    /**
     * @param string $setting
     * @return string|null
     */
    private function getDataLayerGeneralSettingConfig(string $setting)
    {
        return $this->scopeConfig->getValue(
            self::DATALAYER_CONFIGPATH . $setting,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    private function getDataLayerName()
    {
        return $this->getDataLayerGeneralSettingConfig('general_settings/datalayer_name') ?? 'limitless_dl';
    }

    //TODO test throws error
    /**
     * @return array
     */
    public function getAffiliateURLParamList(): array
    {
        $affiliates = $this->getDataLayerGeneralSettingConfig('affiliate_tracking/enabled_affiliates');
        $affiliatesArray = explode("\n", $affiliates);
        $affiliateDataArray = [];
        foreach ($affiliatesArray as $affiliate) {
            $affiliateData = explode(':', $affiliate);
            if (count($affiliateData) === 2) {
                //Builds up array of [Urlkey] => [affiliate code]
                $affiliateDataArray[trim($affiliateData[1])] = trim($affiliateData[0]);
            } else {
                throw new \InvalidArgumentException(sprintf('Incorrect number of arguments for "%s" in configuration, 
                    requires 2 - have %d.', $affiliate, count($affiliateData)));
            }
        }
        return $affiliateDataArray;
    }

    /**
     * @return array
     */
    abstract function getDataLayerVariables(): array;

    abstract function initDataLayerVariables();
}