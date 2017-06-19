<?php

namespace Limitless\Delivery\DeliveryApi;

use Limitless\Delivery\Helper\MetapackRequest;
use Limitless\Delivery\Helper\MetapackOptionsResponse;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class MetapackOptionsApi implements DeliveryApiInterface
{
    private $metapackRequest;
    private $metapackResponse;
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        MetapackRequest $metapackRequest,
        MetapackOptionsResponse $metapackResponse
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->metapackRequest = $metapackRequest;
        $this->metapackResponse = $metapackResponse;
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
     * @param $a
     * @param $b
     * @return int
     */
    private function compareDates($a, $b)
    {
        $dateA = strtotime($a['delivery']['to']);
        $dateB = strtotime($b['delivery']['to']);

        if ($dateA == $dateB) {
            return 0;
        }

        return ($dateA < $dateB) ? -1 : 1;
    }

    /**
     * @param $data
     * @return string
     */
    public function buildRequest($data)
    {
        $customField = $this->metapackRequest->buildCustomField($data);
        $includedGroups = $this->metapackRequest->includedGroups();
        $recipientPhone = ($data['phone'] ? $data['phone'] : '');
        $consignmentValue = ($data['package_value'] ? $data['package_value'] : 0.00);

        $deliveryWeeks = 2;
        if(is_numeric($this->getConfig('carriers/delivery/delivery_weeks'))) {
            $deliveryWeeks = $this->getConfig('carriers/delivery/delivery_weeks');
        }
        $dateTo = date("Y-m-d", strtotime('+'.$deliveryWeeks.' weeks'));

        $request = $this->getConfig('carriers/delivery/url').'/find'.
            '?wh_code='.$this->getConfig('carriers/delivery/warehouse_code').
            '&wh_l1='.$this->getConfig('general/store_information/street_line1').
            '&wh_l2='.$this->getConfig('general/store_information/street_line2').
            '&wh_pc='.$this->getConfig('general/store_information/postcode').
            '&wh_cc='.$this->getConfig('general/store_information/country_id').
            '&acceptableDeliverySlots='.date("Y-m-d").'T00:00:00.000Z,'.$dateTo.'T23:59:59.999Z'.
            '&c_phone='.$recipientPhone.
            '&c_l1='.$data['dest_street'].
            '&c_l2='.$data['dest_city'].
            '&c_pc='.$data['dest_postcode'].
            '&c_cc='.$data['dest_country_id'].
            '&e_v='.$consignmentValue.
            '&e_n=1'.                                       // TODO: Work this out based on contents of basket
            '&e_w='.$data['package_weight'].
            '&e_maxweight='.$data['package_weight'].        // TODO: Rework this based on the max parcel
            $customField.
            $includedGroups.
            '&r_t=ggg'.
            '&r_f=json'.
            '&key='.$this->getConfig('carriers/delivery/key')
        ;

        return str_replace(array("\r\n", "\n", "\r", " "), '+', $request);
    }

    /**
     * @param $request
     * @return array
     */
    public function call($request)
    {
        $curl = curl_init($this->buildRequest($request));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        $deliveryOptions = json_decode($response,true);

        if ($deliveryOptions === false) {
            curl_close($curl);

            return [$this->offlineDeliveryOption()];
        }

        $value = $request['package_value'] ? $request['package_value'] : 0.00;

        return $this->filterResponse($deliveryOptions['results'], $value);
    }

    public function filterResponse($deliveryOptions, $orderValue)
    {
        setlocale(LC_TIME, $this->getConfig('general/locale/code'));

        $filteredDeliveryOptions = [];
        $economyOption = [];
        $timedGroups = explode(',', $this->getConfig('carriers/delivery/timed_groups'));
        $premiumGroups = explode(',', $this->getConfig('carriers/delivery/premium_groups'));
        $economyGroup = $this->getConfig('carriers/delivery/economy_group');

        foreach($deliveryOptions as $deliveryOption) {
            if (in_array($deliveryOption['groupCodes'][0], $timedGroups) || in_array($deliveryOption['groupCodes'][0], $premiumGroups)) {
                list($deliveryOption, $filteredDeliveryOptions) = $this->metapackResponse->buildPremiumDeliveryOption($deliveryOption,
                    $filteredDeliveryOptions);
            } else if ($deliveryOption['groupCodes'][0] == $economyGroup) {
                if (empty($economyOption['shippingCharge']) || $deliveryOption['shippingCharge'] < $economyOption['shippingCharge']) {
                    $economyOption = $this->metapackResponse->buildEconomyDeliveryOption($orderValue, $deliveryOption);
                }
            }
        }

        usort($filteredDeliveryOptions, array($this, 'compareDates'));

        if (!empty($economyOption)) {
            array_unshift($filteredDeliveryOptions, $economyOption);
        }

        return $filteredDeliveryOptions;
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