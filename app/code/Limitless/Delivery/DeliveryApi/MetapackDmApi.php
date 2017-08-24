<?php

namespace Limitless\Delivery\DeliveryApi;

use Limitless\Delivery\DeliveryApi\MetapackDmApi\Service\AllocationService;
use Limitless\Delivery\DeliveryApi\MetapackDmApi\Type\Address;
use Limitless\Delivery\DeliveryApi\MetapackDmApi\Type\AllocationFilter;
use Limitless\Delivery\DeliveryApi\MetapackDmApi\Type\Consignment;
use Limitless\Delivery\Helper\Metapack\Request;
use Limitless\Delivery\Helper\Metapack\DmResponse as Response;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;

class MetapackDmApi extends DeliveryApi
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * MetapackDmApi constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Request $request
     * @param Response $response
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Request $request,
        Response $response,
        ProductRepositoryInterface $productRepository
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->productRepository = $productRepository;
        $this->scopeConfig = $scopeConfig;

        parent::__construct($scopeConfig);
    }

    /**
     * @param $request
     * @param Address $senderAddress
     * @return Address
     */
    private function buildRecipientAddress($request, Address $senderAddress)
    {
        /** @var Address $recipientAddress */
        $recipientAddress = new Address();

        // Possible bug with M2 regarding dest_street and dest_city.
        // Default to senderAddress lines 1 and 2 as just used for obtaining delivery rates
        $recipientAddress->line1 = ($request['dest_street'] != ''  ? $request['dest_street'] : $senderAddress->line1);
        $recipientAddress->line2 = ($request['dest_city'] != ''  ? $request['dest_city'] : $senderAddress->line2);
        $recipientAddress->postCode = $request['dest_postcode'];
        $recipientAddress->countryCode = $request['dest_country_id'];

        return $recipientAddress;
    }

    /**
     * @param $a
     * @param $b
     * @return int
     */
    protected function compareDates($a, $b)
    {
        $dateA = strtotime($a['deliveryWindow']->to);
        $dateB = strtotime($b['deliveryWindow']->to);

        if ($dateA == $dateB) {
            return 0;
        }

        return ($dateA < $dateB) ? -1 : 1;
    }

    /**
     * @param $request
     * @return Consignment
     */
    public function buildRequest(RateRequest $request)
    {
        $senderAddress = $this->request->buildSenderAddress();
        $recipientAddress = $this->buildRecipientAddress($request,$senderAddress);
        $customField = $this->request->buildCustomField($request,'dm');

        /** @var Consignment $consignment */
        $consignment = new Consignment();
        $consignment->cashOnDeliveryCurrency = $this->getConfig('currency/options/base');
        $consignment->CODAmount = 0.00;
        $consignment->CODFlag = 0;
        $consignment->consignmentLevelDetailsFlag = 1;
        $consignment->consignmentValue = ($request['value'] ? $request['value'] : 0.00);
        $consignment->consignmentWeight = $request['package_weight'];
        $consignment->languageCode = strtoupper(explode('_',$this->getConfig('general/locale/code'))[0]);
        $consignment->maxDimension = $this->request->getMaxDimension($request);
        $consignment->orderNumber = ($request['order_number'] ? $request['order_number'] : '123456');
        $consignment->orderValue = ($request['value'] ? $request['value'] : 0.00);
        $consignment->parcelCount = $this->request->parcelCount($request);
        $consignment->recipientAddress = $recipientAddress;
        $consignment->recipientContactPhone = ($request['phone'] ? $request['phone'] : $this->getConfig('general/store_information/phone'));
        $consignment->recipientEmail = '';
        $consignment->recipientFirstName = ($request['first_name'] ? $request['first_name'] : '');
        $consignment->recipientLastName = ($request['last_name'] ? $request['last_name'] : '');
        $consignment->recipientMobilePhone = '';
        $consignment->recipientName = $request['first_name'] . ' ' . $request['last_name'];
        $consignment->recipientPhone = ($request['phone'] ? $request['phone'] : $this->getConfig('general/store_information/phone'));
        $consignment->recipientTimeZone = '';
        $consignment->recipientTitle = '';
        $consignment->senderAddress = $senderAddress;
        $consignment->senderCode = $this->getConfig('carriers/delivery_metapack/warehouse_code', true);
        $consignment->senderFirstName = $this->getConfig('general/store_information/name');
        $consignment->senderLastName = '';
        $consignment->senderName = $this->getConfig('general/store_information/name');
        $consignment->senderPhone = $this->getConfig('general/store_information/phone');
        $consignment->transactionType = 'delivery';
        $consignment->twoManLiftFlag = 0;

        if ($customField) {
            $consignment->custom6 = $customField;
        }

        return $consignment;
    }

    /**
     * @return AllocationFilter
     */
    private function buildAllocationFilter()
    {
        /** @var AllocationFilter $allocationFilter */
        $allocationFilter = new AllocationFilter();
        $allocationFilter->acceptableDeliverySlots = date("Y-m-d").'T00:00:00.000Z,'. $this->getDateTo() .'T23:59:59.999Z';
        $includedGroups = $this->request->includedGroups();

        if ($includedGroups) {
            $allocationFilter->acceptableCarrierServiceGroupCodes = $includedGroups;
        }

        $allocationFilter->sortOrder1 = $this->getConfig('carriers/delivery/sort_order1');
        $allocationFilter->sortOrder2 = $this->getConfig('carriers/delivery/sort_order2');
        $allocationFilter->sortOrder3 = $this->getConfig('carriers/delivery/sort_order3');

        return $allocationFilter;
    }

    /**
     * @param $request
     * @return array
     */
    public function call(RateRequest $request): array
    {
        if ($request['dest_postcode'] === '*' || $request['dest_postcode'] == '') {
            return [$this->offlineDeliveryOption()];
        }

        /** @var AllocationService $allocationService */
        $allocationService = new AllocationService($this->getConfig('carriers/delivery/wsdl', true).'AllocationService?wsdl',array("login" => $this->getConfig('carriers/delivery/username'), "password" => $this->getConfig('carriers/delivery/password')));
        $deliveryOptions = $allocationService->findDeliveryOptions($this->buildRequest($request),$this->buildAllocationFilter(),0);
        $value = $request['package_value'] ?? 0.00;

        return $this->filterResponse($deliveryOptions, $value);
    }

    /**
     * @param $deliveryOptions
     * @param $orderValue
     * @return array
     */
    public function filterResponse($deliveryOptions, $orderValue): array
    {
        setlocale(LC_TIME, $this->getConfig('general/locale/code'));

        $groupDateMapping = [];
        $filteredDeliveryOptions = [];
        $economyOption = [];
        $timedGroups = explode(',', $this->getConfig('carriers/delivery_metapack/timed_groups'));
        $premiumGroups = explode(',', $this->getConfig('carriers/delivery_metapack/premium_groups', true));
        $economyGroup = $this->getConfig('carriers/delivery_metapack/economy_group', true);

        foreach ($deliveryOptions as $deliveryOption) {
            if (!is_array($deliveryOption)) {
                $deliveryOption = get_object_vars($deliveryOption);
            }
            foreach ($deliveryOption['groupCodes'] as $groupCode) {
                if (in_array($groupCode, $timedGroups) || in_array($groupCode, $premiumGroups)) {
                    $date = explode('T',$deliveryOption['deliveryWindow']->to)[0];
                    if (isset($groupDateMapping[$groupCode])) {
                        foreach ($groupDateMapping[$groupCode] as $k => $v) {
                            if ($v['date'] === $date) {
                                continue 2;
                            }
                        }
                    }
                    $groupDateMapping[$groupCode][] = [
                        'date' => $date,
                        'shipping_charge' => $deliveryOption['shippingCharge']
                    ];
                    $deliveryOption['groupCode'] = $groupCode;
                    $filteredDeliveryOptions[] = $this->response->buildPremiumDeliveryOption($deliveryOption, $filteredDeliveryOptions);
                }  else if ($groupCode == $economyGroup) {
                    if (empty($economyOption['shippingCharge']) || $deliveryOption['shippingCharge'] < $economyOption['shippingCharge']) {
                        $economyOption = $this->response->buildEconomyDeliveryOption($orderValue, $deliveryOption);
                    }
                }
            }
        }

        usort($filteredDeliveryOptions, array($this, 'compareDates'));

        if (!empty($economyOption)) {
            array_unshift($filteredDeliveryOptions, $economyOption);
        }

        return $filteredDeliveryOptions;
    }

    /**
     * @return false|string
     */
    private function getDateTo()
    {
        $deliveryWeeks = 2;
        $deliveryWeeksCompare = $this->getConfig('carriers/delivery/delivery_weeks', true);
        if (is_numeric($deliveryWeeksCompare) && $deliveryWeeksCompare > 0) {
            $deliveryWeeks = $deliveryWeeksCompare;
        }
        $dateTo = date("Y-m-d", strtotime('+' . $deliveryWeeks . ' weeks'));
        return $dateTo;
    }

}