<?php
/**
 * Solvitt Currency Import Service
 *
 * @author     Kevin Varley <kevin.varley@limitlessdigital.com>
 */
namespace Limitless\CurrencyImportServices\Model\Currency\Import;

class Solvitt extends \Magento\Directory\Model\Currency\Import\AbstractImport
{
    
    protected $_scopeConfig, $_soapClient, $_soapClientOptions, $_soapRequestParametersDefault;

    /**
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct (
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($currencyFactory);
        $this->_scopeConfig = $scopeConfig;

        $soapTimeout = $this->_scopeConfig->getValue(
            'currency/solvitt/timeout',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $this->_soapOptionsDefault = array('Database' => 'SBS');
        $this->_soapRequestParametersDefault = array('cache_wsdl' => WSDL_CACHE_NONE, 'connection_timeout' => $soapTimeout);

        $solvittWsdlUrl = $this->_scopeConfig->getValue(
            'currency/solvitt/wsdl',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $this->_soapClient = new \SoapClient($solvittWsdlUrl, $this->_soapRequestParametersDefault);
    }

    protected function getCurrencyRate ($currencyTo) {
        $soapResponse = $this->_soapClient->ExchangeRateEnquiry($this->_soapOptionsDefault);

        $soapResponseTree = simplexml_load_string($soapResponse->ExchangeRateEnquiryResult->any);

        $solvittExchangeRate = (float)$soapResponseTree->xpath('//Abbreviation[.="' . $currencyTo . '"]/parent::*')[0]->ExchangeRate[0];

        if (!is_numeric($solvittExchangeRate)) {
            return false;
        }

        return round($solvittExchangeRate, 4, PHP_ROUND_HALF_UP);
    }

    /**
     * @param string $currencyFrom
     * @param string $currencyTo
     * @param int $retry
     * @return float|null
     */
    protected function _convert($currencyFrom, $currencyTo, $retry = 0) {
        return $this->getCurrencyRate($currencyTo);
    }
}
