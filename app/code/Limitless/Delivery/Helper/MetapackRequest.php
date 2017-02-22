<?php

namespace Limitless\Delivery\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class MetapackRequest
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

        if($customField !== '') {
            $customField = '&custom6=' . $customField;
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

        return $includedGroups != '' ? '&incgrp=' . $includedGroups : '';
    }
}