<?php

namespace Limitless\Metapack\Helper;

use Limitless\Metapack\Helper\Type\Consignment;
use Limitless\Metapack\Helper\Type\AllocationFilter;
use Limitless\Metapack\Helper\Type\Address;
use Limitless\Metapack\Helper\Type\Parcel;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    public function getStoreName()
    {
        return $this->scopeConfig->getValue(
            'general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getStorePhoneNumber()
    {
        return $this->scopeConfig->getValue(
            'general/store_information/phone',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getStoreCountry()
    {
        return $this->scopeConfig->getValue(
          'general/store_information/country_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getStoreRegion()
    {
        return $this->scopeConfig->getValue(
            'general/store_information/region_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getStorePostcode()
    {
        return $this->scopeConfig->getValue(
            'general/store_information/postcode',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getStoreCity()
    {
        return $this->scopeConfig->getValue(
            'general/store_information/city',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getStoreStreetLine1()
    {
        return $this->scopeConfig->getValue(
            'general/store_information/street_line1',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getStoreStreetLine2()
    {
        return $this->scopeConfig->getValue(
            'general/store_information/street_line2',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getStoreLocale()
    {
        return $this->scopeConfig->getValue(
            'general/locale/code',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getStoreBaseCurrency()
    {
        return $this->scopeConfig->getValue(
            'currency/options/base',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getWarehouseCode()
    {
        return $this->scopeConfig->getValue(
            'carriers/metapack/warehouse_code',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getUnacceptableCarrierServiceGroupCodes()
    {
        return $this->scopeConfig->getValue(
            'carriers/metapack/unacceptable_carrier_service_group_codes',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $carrierCode
     * @return mixed
     */
    public function mapCarrierName($carrierCode)
    {
        //Add this into grid in admin and pull out from there (going forwards)
        $carrierNameMap = array(
            'DHLINT'    => 'DHL',
            'DPD'       => 'DPD',
            'FEDEX'     => 'FedEx',
            'GEO01'     => 'Geodis',
            'HERMESPOS' => 'Hermes',
            'HERCXB'    => 'Hermes International',
            'NF1MAN'    => 'DX',
            'NF2MAN'    => 'DX 2 Man',
            'ROYALMAIL' => 'Royal Mail',
            'WNINT'     => 'wnDirect'
        );

        if(array_key_exists($carrierCode,$carrierNameMap)) {
            $carrierName = $carrierNameMap[$carrierCode];
        } else {
            $carrierName = $carrierCode;        // Default to the carrier code
        }

        return $carrierName;
    }

    /**
     * @return AllocationFilter
     */
    public function buildAllocationFilter()
    {
        $allocationFilter = new AllocationFilter();
        $allocationFilter->filterGroup1 = 1;
        $allocationFilter->filterGroup2 = 2;
        $allocationFilter->filterGroup3 = 3;
        $allocationFilter->unacceptableCarrierServiceGroupCodes = explode(',',$this->getUnacceptableCarrierServiceGroupCodes());

        return $allocationFilter;
    }
    
    /**
     * @return Address
     */
    public function buildSenderAddress()
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
    public function buildRecipientAddress($request, Address $senderAddress)
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
     * @param $parcelDetails
     * @return Parcel
     */
    public function buildParcel($parcelDetails)
    {
        $parcel = new Parcel();
        
        $parcel->parcelValue = $parcelDetails['value'];
        $parcel->parcelWeight = $parcelDetails['weight'];

        return $parcel;
    }

    /**
     * @param $request
     * @return Consignment
     */
    public function buildConsignment($request,$parcels = null)
    {
        $senderAddress = $this->buildSenderAddress();
        $recipientAddress = $this->buildRecipientAddress($request,$senderAddress);

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
        $consignment->podRequired = 'signature';
        $consignment->recipientAddress = $recipientAddress;
        $consignment->recipientContactPhone = '';
        $consignment->recipientEmail = '';
        $consignment->recipientFirstName = ($request['first_name'] ? $request['first_name'] : '');
        $consignment->recipientLastName = ($request['last_name'] ? $request['last_name'] : '');
        $consignment->recipientMobilePhone = '';
        $consignment->recipientName = $request['first_name'] . ' ' . $request['last_name'];
        $consignment->recipientPhone = ($request['phone'] ? $request['phone'] : '');
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

}