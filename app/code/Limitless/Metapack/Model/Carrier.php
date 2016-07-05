<?php
namespace Limitless\Metapack\Model;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Magento\Framework\Module\Dir;
use Limitless\Metapack\Helper\Service\AllocationService;
use Limitless\Metapack\Helper\Data;


class Carrier extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{

    protected $_code = 'metapack';
    protected $_helper;

    /**
     * Carrier constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Limitless\Metapack\Helper\Data $helper,
        array $data = []
    )
    {
        $this->_helper = $helper;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;

        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    public function getAllowedMethods()
    {
        return ['metapack' => $this->getConfigData('title')];
    }

    /**
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request)
    {
        if(!$this->getConfigFlag('active')) {
            return false;
        }

        $allocationService = new AllocationService($this->getConfigData('wsdl').'AllocationService?wsdl',array("login" => $this->getConfigData('username'), "password" => $this->getConfigData('password')));
        $deliveryOptions = $allocationService->findDeliveryOptions($this->_helper->buildConsignment($request),$this->_helper->buildAllocationFilter(),0);

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        foreach($deliveryOptions as $deliveryOption) {

            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier('metapack');
            $method->setCarrierTitle($deliveryOption->name);
            $method->setMethod($deliveryOption->carrierServiceCode);
            $method->setMethodTitle($this->_helper->mapCarrierName($deliveryOption->carrierCode));

            $amount = $deliveryOption->shippingCost + $this->getConfigData('handling_fee');

            $method->setPrice($amount);
            $method->setCost($amount);

            $result->append($method);
        }

        return $result;
    }
    
}