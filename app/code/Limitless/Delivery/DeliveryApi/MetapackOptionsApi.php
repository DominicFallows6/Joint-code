<?php

namespace Limitless\Delivery\DeliveryApi;

use Limitless\Delivery\Helper\Metapack\Request;
use Limitless\Delivery\Helper\Metapack\OptionsResponse as Response;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;

class MetapackOptionsApi extends DeliveryApi
{
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
     * MetapackOptionsApi constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Request $request
     * @param Response $response
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Request $request,
        Response $response
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->response = $response;

        parent::__construct($scopeConfig);
    }

    /**
     * @param $a
     * @param $b
     * @return int
     */
    protected function compareDates($a, $b)
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
    public function buildRequest(RateRequest $data)
    {
        $customField = $this->request->buildCustomField($data);
        $includedGroups = $this->request->includedGroups();
        $recipientPhone = ($data['phone'] ? $data['phone'] : '');
        $consignmentValue = ($data['package_value'] ? $data['package_value'] : 0.00);

        $deliveryWeeks = 2;
        if(is_numeric($this->getConfig('carriers/delivery/delivery_weeks', true))) {
            $deliveryWeeks = $this->getConfig('carriers/delivery/delivery_weeks', true);
        }
        $dateTo = date("Y-m-d", strtotime('+'.$deliveryWeeks.' weeks'));

        $request = $this->getConfig('carriers/delivery_metapack_options/url', true).'/find'.
            '?wh_code='.$this->getConfig('carriers/delivery_metapack/warehouse_code', true).
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
            '&e_n=1'.
            '&e_w='.$data['package_weight'].
            '&e_maxweight='.$data['package_weight'].
            $customField.
            $includedGroups.
            '&r_t=ggg'.
            '&r_f=json'.
            '&key='.$this->getConfig('carriers/delivery_metapack_options/key', true)
        ;

        return str_replace(array("\r\n", "\n", "\r", " "), '+', $request);
    }

    /**
     * @param $request
     * @return array
     */
    public function call(RateRequest $request): array
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

    /**
     * @param $deliveryOptions
     * @param $orderValue
     * @return array
     */
    public function filterResponse($deliveryOptions, $orderValue): array
    {
        setlocale(LC_TIME, $this->getConfig('general/locale/code'));

        $filteredDeliveryOptions = [];
        $economyOption = [];
        $timedGroups = explode(',', $this->getConfig('carriers/delivery_metapack/timed_groups', true));
        $premiumGroups = explode(',', $this->getConfig('carriers/delivery_metapack/premium_groups', true));
        $economyGroup = $this->getConfig('carriers/delivery_metapack/economy_group', true);

        foreach($deliveryOptions as $deliveryOption) {
            if (in_array($deliveryOption['groupCodes'][0], $timedGroups) || in_array($deliveryOption['groupCodes'][0], $premiumGroups)) {
                list($deliveryOption, $filteredDeliveryOptions) = $this->response->buildPremiumDeliveryOption($deliveryOption, $filteredDeliveryOptions);
            } else if ($deliveryOption['groupCodes'][0] == $economyGroup) {
                if (empty($economyOption['shippingCharge']) || $deliveryOption['shippingCharge'] < $economyOption['shippingCharge']) {
                    $economyOption = $this->response->buildEconomyDeliveryOption($orderValue, $deliveryOption);
                }
            }
        }

        usort($filteredDeliveryOptions, array($this, 'compareDates'));

        if (!empty($economyOption)) {
            array_unshift($filteredDeliveryOptions, $economyOption);
        }

        return $filteredDeliveryOptions;
    }

}