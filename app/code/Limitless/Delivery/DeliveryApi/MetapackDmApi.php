<?php

namespace Limitless\Delivery\DeliveryApi;

use Limitless\Delivery\DeliveryApi\MetapackDmApi\Service\AllocationService;
use Limitless\Delivery\DeliveryApi\MetapackDmApi\Type\Address;
use Limitless\Delivery\DeliveryApi\MetapackDmApi\Type\AllocationFilter;
use Limitless\Delivery\DeliveryApi\MetapackDmApi\Type\Consignment;
use Limitless\Delivery\Helper\MetapackRequest;
use Limitless\Delivery\Helper\MetapackDmResponse;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class MetapackDmApi implements DeliveryApiInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    private $metapackRequest;
    private $metapackResponse;
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        MetapackRequest $metapackRequest,
        MetapackDmResponse $metapackResponse,
        ProductRepositoryInterface $productRepository
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->metapackRequest = $metapackRequest;
        $this->metapackResponse = $metapackResponse;
        $this->productRepository = $productRepository;
    }

    /**
     * @param string $configPath
     * @return string|null
     */
    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $data
     * @return string
     */
    public function buildRequest($data, $parcels = null)
    {
        $senderAddress = $this->buildSenderAddress();
        $recipientAddress = $this->buildRecipientAddress($data,$senderAddress);

        $consignment = new Consignment();
        $consignment->cashOnDeliveryCurrency = $this->getStoreBaseCurrency();
        $consignment->CODAmount = 0.00;
        $consignment->CODFlag = 0;
        $consignment->consignmentLevelDetailsFlag = 1;
        $consignment->consignmentValue = ($data['value'] ? $data['value'] : 0.00);
        $consignment->consignmentWeight = $data['package_weight'];
        $consignment->languageCode = strtoupper(explode('_',$this->getStoreLocale())[0]);
        $consignment->orderNumber = ($data['order_number'] ? $data['order_number'] : '123456');
        $consignment->orderValue = ($data['value'] ? $data['value'] : 0.00);
        $consignment->parcelCount = 1;
        //$consignment->podRequired = 'signature';
        $consignment->recipientAddress = $recipientAddress;
        $consignment->recipientContactPhone = '';
        $consignment->recipientEmail = '';
        $consignment->recipientFirstName = ($data['first_name'] ? $data['first_name'] : '');
        $consignment->recipientLastName = ($data['last_name'] ? $data['last_name'] : '');
        $consignment->recipientMobilePhone = '';
        $consignment->recipientName = $data['first_name'] . ' ' . $data['last_name'];
        $consignment->recipientPhone = ($data['phone'] ? $data['phone'] : '');
        $consignment->recipientTimeZone = '';
        $consignment->recipientTitle = '';
        $consignment->senderAddress = $senderAddress;
        $consignment->senderCode = $this->getWarehouseCode();
        $consignment->senderFirstName = $this->getStoreName();
        $consignment->senderLastName = '';
        $consignment->senderName = $this->getStoreName();
        $consignment->senderPhone = $this->getStorePhoneNumber();
        $consignment->transactionType = 'delivery';
        $consignment->twoManLiftFlag = 0;

        if($parcels !== null) {
            $consignment->parcels = $parcels;
        }

        return $consignment;
    }

    // TODO: Get rid of these utility functions and call getConfig directly?
    private function getStoreName()
    {
        return $this->getConfig('general/store_information/name');
    }

    private function getStoreCountry()
    {
        return $this->getConfig('general/store_information/country_id');
    }

    private function getStoreStreetLine1()
    {
        return $this->getConfig('general/store_information/street_line1');
    }

    private function getStoreStreetLine2()
    {
        return $this->getConfig('general/store_information/street_line2');
    }

    private function getStoreCity()
    {
        return $this->getConfig('general/store_information/city');
    }

    private function getStorePostcode()
    {
        return $this->getConfig('general/store_information/postcode');
    }

    private function getStoreRegion()
    {
        return $this->getConfig('general/store_information/region_id');
    }

    private function getStorePhoneNumber()
    {
        return $this->getConfig('general/store_information/phone');
    }

    private function getStoreBaseCurrency()
    {
        return $this->getConfig('currency/options/base');
    }

    private function getStoreLocale()
    {
        return $this->getConfig('general/locale/code');
    }

    /**
     * @param $a
     * @param $b
     * @return int
     */
    private function compareDates($a, $b)
    {
        $dateA = strtotime($a['deliveryWindow']->to);
        $dateB = strtotime($b['deliveryWindow']->to);

        if ($dateA == $dateB) {
            return 0;
        }

        return ($dateA < $dateB) ? -1 : 1;
    }

    private function getWarehouseCode()
    {
        return $this->getConfig('carriers/delivery/warehouse_code');
    }

    // TODO: Create Interface for Helper/MetapackRequest ansd split into Options and DM, then add this into respective class
    /**
     * @return Address
     */
    private function buildSenderAddress()
    {
        $senderAddress = new Address();

        $senderAddress->companyName = $this->getStoreName();
        $senderAddress->countryCode = $this->getStoreCountry();
        $senderAddress->line1 = $this->getStoreStreetLine1();
        $senderAddress->line2 = $this->getStoreStreetLine2();
        $senderAddress->line3 = $this->getStoreCity();
        $senderAddress->postCode = $this->getStorePostcode();
        $senderAddress->region = $this->getStoreRegion();
        $senderAddress->type = 'Business';

        return $senderAddress;
    }

    /**
     * @param $request
     * @param Address $senderAddress
     * @return Address
     */
    private function buildRecipientAddress($request, Address $senderAddress)
    {
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
     * @param $request
     * @return Consignment
     */
    private function buildConsignment($request,$parcels = null)
    {
        $senderAddress = $this->buildSenderAddress();
        $recipientAddress = $this->buildRecipientAddress($request,$senderAddress);
        $customField = $this->buildCustomField($request);

        $consignment = new Consignment();
        $consignment->cashOnDeliveryCurrency = $this->getStoreBaseCurrency();
        $consignment->CODAmount = 0.00;
        $consignment->CODFlag = 0;
        $consignment->consignmentLevelDetailsFlag = 1;
        $consignment->consignmentValue = ($request['value'] ? $request['value'] : 0.00);
        $consignment->consignmentWeight = $request['package_weight'];
        $consignment->languageCode = strtoupper(explode('_',$this->getStoreLocale())[0]);
        $consignment->orderNumber = ($request['order_number'] ? $request['order_number'] : '123456');
        $consignment->orderValue = ($request['value'] ? $request['value'] : 0.00);
        $consignment->parcelCount = 1;
        //$consignment->podRequired = 'signature';
        $consignment->recipientAddress = $recipientAddress;
        $consignment->recipientContactPhone = ($request['phone'] ? $request['phone'] : $this->getStorePhoneNumber());
        $consignment->recipientEmail = '';
        $consignment->recipientFirstName = ($request['first_name'] ? $request['first_name'] : '');
        $consignment->recipientLastName = ($request['last_name'] ? $request['last_name'] : '');
        $consignment->recipientMobilePhone = '';
        $consignment->recipientName = $request['first_name'] . ' ' . $request['last_name'];
        $consignment->recipientPhone = ($request['phone'] ? $request['phone'] : $this->getStorePhoneNumber());
        $consignment->recipientTimeZone = '';
        $consignment->recipientTitle = '';
        $consignment->senderAddress = $senderAddress;
        $consignment->senderCode = $this->getWarehouseCode();
        $consignment->senderFirstName = $this->getStoreName();
        $consignment->senderLastName = '';
        $consignment->senderName = $this->getStoreName();
        $consignment->senderPhone = $this->getStorePhoneNumber();
        $consignment->transactionType = 'delivery';
        $consignment->twoManLiftFlag = 0;

        if($parcels !== null) {
            $consignment->parcels = $parcels;
        }

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
        $allocationFilter = new AllocationFilter();
        $allocationFilter->acceptableDeliverySlots = date("Y-m-d").'T00:00:00.000Z,'. $this->getDateTo() .'T23:59:59.999Z';
        $includedGroups = $this->includedGroups();
        if ($includedGroups) {
            $allocationFilter->acceptableCarrierServiceGroupCodes = $includedGroups;
        }

        //TODO: Add custom field

        return $allocationFilter;
    }

    // TODO: This needs adding into seperare 'request' helper (as per options)
    public function buildCustomField($data)
    {
        $customField = '';

        /** @var \Magento\Quote\Model\Quote\Address\RateRequest $data */
        $items = $data->getAllItems();
        if (is_array($items)) {
            foreach ($items as $item) {
                $product = $this->productRepository->getById($item->getProductId());
                if ($product->getPallet() == 1 && strpos($customField,'PALLET') === false) {
                    if(strlen($customField) > 0) {
                        $customField .= ',PALLET';
                    } else {
                        $customField = 'PALLET';
                    }
                }
                if ($product->getTwoman() == 1 && strpos($customField,'TWOMAN') === false) {
                    if(strlen($customField) > 0) {
                        $customField .= ',TWOMAN';
                    } else {
                        $customField = 'TWOMAN';
                    }
                }
            }
        }

        return $customField;
    }

    /**
     * @return string
     */
    public function includedGroups()
    {
        $includedGroups = '';
        $premiumGroups = $this->getConfig('carriers/delivery/premium_groups');
        $economyGroup = $this->getConfig('carriers/delivery/economy_group');

        if ($economyGroup != '') {
            $includedGroups = $economyGroup;
        }

        if ($premiumGroups != '') {
            $includedGroups = ($includedGroups != '' ? $includedGroups . ',' . $premiumGroups : $premiumGroups);
        }

        //$includedGroups = ['ECONOMY','NEXTDAY','NEXTDAY12','NEXTDAY930','SAT930','SATURDAYPM','SATURDAYAM','NEXTDAYMORNING','NEXTDAYAFTERNOON','NEXTDAYEVENING','NEXTDAYEARLYMORNING'];
        //return ['JAKETEST'];

        return explode(',', $includedGroups);
    }

    public function call($request)
    {
        if ($request['dest_postcode'] === '*') {
            return [$this->offlineDeliveryOption()];
        }
        $allocationService = new AllocationService($this->getConfig('carriers/delivery/wsdl').'AllocationService?wsdl',array("login" => $this->getConfig('carriers/delivery/username'), "password" => $this->getConfig('carriers/delivery/password')));
        $deliveryOptions = $allocationService->findDeliveryOptions($this->buildConsignment($request),$this->buildAllocationFilter($request),0);

        $value = $request['package_value'] ? $request['package_value'] : 0.00;

        return $this->filterResponse($deliveryOptions, $value);
    }

    public function filterResponse($deliveryOptions, $orderValue)
    {
        setlocale(LC_TIME, $this->getConfig('general/locale/code'));

        $groupDateMapping = [];
        $filteredDeliveryOptions = [];
        $economyOption = [];
        $premiumGroups = explode(',', $this->getConfig('carriers/delivery/premium_groups'));
        $economyGroup = $this->getConfig('carriers/delivery/economy_group');

        foreach ($deliveryOptions as $deliveryOption) {
            if (!is_array($deliveryOption)) {
                $deliveryOption = get_object_vars($deliveryOption);
            }
            foreach ($deliveryOption['groupCodes'] as $groupCode) {
                if (in_array($groupCode, $premiumGroups)) {
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
                    $filteredDeliveryOptions[] = $this->metapackResponse->buildPremiumDeliveryOption($deliveryOption, $groupCode);
                }  else if ($groupCode == $economyGroup) {
                    if (empty($economyOption['shippingCharge']) || $deliveryOption['shippingCharge'] < $economyOption['shippingCharge']) {
                        $economyOption = $this->metapackResponse->buildEconomyDeliveryOption($orderValue, $deliveryOption);
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
        if (is_numeric($this->getConfig('carriers/delivery/delivery_weeks'))) {
            $deliveryWeeks = $this->getConfig('carriers/delivery/delivery_weeks');
        }
        $dateTo = date("Y-m-d", strtotime('+' . $deliveryWeeks . ' weeks'));
        return $dateTo;
    }

    private function offlineDeliveryOption()
    {
        $offlineOption = [];
        $offlineOption['deliveryTimeString'] = (__('3 - 5 Working days'));
        $offlineOption['allocationFilter'] = $this->getConfig('carriers/delivery/economy_group');
        $offlineOption['deliveryServiceLevelString'] = (__('Standard delivery'));
        $offlineOption['shippingCharge'] = $this->getConfig('carriers/delivery/economy_group_price');

        return $offlineOption;
    }
}