<?php
namespace Limitless\Delivery\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Rate\Result;
use Limitless\Delivery\DeliveryApi\DeliveryApiInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;


class Carrier extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{

    public $_code = 'delivery';
    
    protected $deliveryApi;
    protected $rateResultFactory;
    protected $rateMethodFactory;
    protected $connect;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Carrier constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param DeliveryApi $deliveryApi
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        DeliveryApiInterface $deliveryApi,
        array $data = []
    )
    {
        $this->deliveryApi = $deliveryApi;
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;

        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    public function getAllowedMethods()
    {
        return ['delivery' => $this->getConfigData('title')];
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
        
        $deliveryOptions = $this->deliveryApi->call($request);
        
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();

        if($deliveryOptions !== false) {
            foreach ($deliveryOptions as $deliveryOption) {

                /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
                $method = $this->rateMethodFactory->create();

                $method->setCarrier('delivery');
                $method->setCarrierTitle($deliveryOption['deliveryServiceLevelString']);
                $method->setMethod($deliveryOption['allocationFilter']);
                $method->setMethodTitle($deliveryOption['deliveryTimeString']);

                $amount = $deliveryOption['shippingCharge'];

                $method->setPrice($amount);
                $method->setCost($amount);

                $result->append($method);
            }
        }

        return $result;
    }

}