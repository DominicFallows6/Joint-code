<?php

namespace Limitless\Delivery\Helper\Metapack;

use Limitless\Delivery\DeliveryApi\MetapackDmApi\Type\Address;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Request
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ProductRepositoryInterface $productRepository
    ) {
        $this->scopeConfig = $scopeConfig;
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

    public function buildCustomField($data, $endpoint = 'options')
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

        if($endpoint === 'options' && $customField !== '') {
            $customField = '&custom6=' . $customField;
        }

        return $customField;
    }

    /**
     * @param $data
     * @return int
     */
    public function getMaxDimension($data)
    {
        $maxLength = $maxWidth = $maxHeight = 0;

        foreach ($data['all_items'] as $item) {
            $product = $this->productRepository->getById($item->getProductId());

            $maxLength = ($product->getLength() > $maxLength) ? $product->getLength() : $maxLength;
            $maxWidth = ($product->getWidth() > $maxWidth) ? $product->getWidth() : $maxWidth;
            $maxHeight = ($product->getHeight() > $maxHeight) ? $product->getHeight() : $maxHeight;
        }

        return max($maxLength,$maxWidth,$maxHeight);
    }

    public function parcelCount($data)
    {
        // Return estimated number of parcels in Customer's basket (based on max parcel weight set in admin)
        $parcelMaxWeight = $this->getConfig('carriers/delivery/max_parcel_weight');

        // Create array of weights (keeping in mind quantities)
        $weights = [];
        foreach ($data['all_items'] as $item) {
            for ($i = 0; $i < $item['qty']; $i++) {
                $weights[] = $item['weight'];
            }
        }

        // Sort, lightest to heaviest
        sort($weights);

        // Bundle into parcels up to specified weight
        $parcelNo = 1;
        $parcels = [1 => 0];
        foreach ($weights as $weight) {
            if ($parcels[$parcelNo] === 0 || ($parcels[$parcelNo] + $weight) <= $parcelMaxWeight) {
                $parcels[$parcelNo] += $weight;
            } else {
                $parcels[++$parcelNo] = $weight;
            }
        }

        return count($parcels);
    }

    /**
     * @param string $endpoint
     * @return array|string
     */
    public function includedGroups($endpoint = 'options')
    {
        $includedGroups = '';
        $timedGroups = $this->getConfig('carriers/delivery_metapack/timed_groups');
        $premiumGroups = $this->getConfig('carriers/delivery_metapack/premium_groups');
        $economyGroup = $this->getConfig('carriers/delivery_metapack/economy_group');

        if ($economyGroup != '') {
            $includedGroups = $economyGroup;
        }

        if ($premiumGroups != '') {
            $includedGroups = ($includedGroups != '' ? $includedGroups . ',' . $premiumGroups : $premiumGroups);
        }

        if ($timedGroups != '') {
            $includedGroups = ($includedGroups != '' ? $includedGroups . ',' . $timedGroups : $timedGroups);
        }

        if ($endpoint === 'options') {
            return $includedGroups != '' ? '&incgrp=' . $includedGroups : '';
        } else {
            return explode(',', $includedGroups);
        }
    }

    /**
     * @return Address
     */
    public function buildSenderAddress()
    {
        /** @var Address $senderAddress */
        $senderAddress = new Address();

        $senderAddress->companyName = $this->getConfig('general/store_information/name');
        $senderAddress->countryCode = $this->getConfig('general/store_information/country_id');
        $senderAddress->line1 = $this->getConfig('general/store_information/street_line1');
        $senderAddress->line2 = $this->getConfig('general/store_information/street_line2');
        $senderAddress->line3 = $this->getConfig('general/store_information/city');
        $senderAddress->postCode = $this->getConfig('general/store_information/postcode');
        $senderAddress->region = $this->getConfig('general/store_information/region_id');
        $senderAddress->type = 'Business';

        return $senderAddress;
    }
}