<?php

namespace Limitless\TagManagerDataLayer\Helper\TagsDataLayer;

use Magento\Framework\View\Element\Template\Context;

class LeGuide
{
    const LG_GENERAL_SETTINGS_CONFIGPATH = 'google/limitless_tagmanager_datalayer/leguide/';

    const LG_DATALAYER_NAME = 'leguide';

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    private $scopeConfig;

    /** @var array */
    private $leGuideProductSkus;

    /** @var array */
    private $leGuideProductPrices;

    /** @var array */
    private $leGuideProductQtys;

    public function __construct(Context $context)
    {
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * @param \Magento\Sales\Model\Order\Item[] $orderItems
     */
    public function buildLeGuideValues($orderItems)
    {
        if ($this->getEnabled()) {
            $delimiter = ',';

            $productSkus = $productPrices = $productQtys = [];

            foreach ($orderItems as $productItem) {

                $itemCode = $this->getLeguideItemCode($productItem);

                $productSkus[] = htmlspecialchars($itemCode);
                $productPrices[] = $this->getLeGuideItemPrice($productItem);
                $productQtys[] = intval($productItem->getQtyOrdered());
            }

            $this->leGuideProductSkus = '\\'.implode($delimiter, $productSkus);
            $this->leGuideProductPrices = '\\'.implode($delimiter, $productPrices);
            $this->leGuideProductQtys = '\\'.implode($delimiter, $productQtys);
        }
    }

    public function getLeGuideValues(): array
    {
        if ($this->getEnabled()) {
            return [self::LG_DATALAYER_NAME =>
                    [
                        'productlistSkus' => $this->leGuideProductSkus,
                        'productlistPrices' => $this->leGuideProductPrices,
                        'productlistQtys' => $this->leGuideProductQtys
                    ]
                ];
        } else {
            return [];
        }
    }

    /**
     * @param $productItem
     * @return string
     */
    private function getLeGuideItemPrice($productItem): string
    {
        $vatSetting = $this->getLeguideVATSetting();
        $productItemPrice = $productItem->getPriceInclTax();

        switch ($vatSetting) {
            case 'exclude':
                $productItemPrice -= ($productItem->getTaxAmount() / $productItem->getQtyOrdered());
                break;
        }
        return $this->ukNumberFormat($productItemPrice);
    }

    /**
     * @param $orderItem
     * @return string
     */
    private function getLeguideItemCode($productItem): string
    {
        $productIdValueSetting = $this->getLeguideProductIdValue();

        switch ($productIdValueSetting) {
            case 'id':
                $ecommProdId = $productItem->getProductId();
                break;
            case 'alias_fallback_sku':
                $orderItem = $productItem->getProduct();
                $itemCode = $orderItem->getSku();
                if (!empty($orderItem->getData('alias'))) {
                    $itemCode = $orderItem->getData('alias');
                }
                $ecommProdId = htmlspecialchars($itemCode);
                break;
            case 'sku':
            default:
                $ecommProdId = htmlspecialchars($productItem->getSku());
                break;
        }
        return $ecommProdId;
    }

    /**
     * @param $number
     * @return string
     */
    private function ukNumberFormat($number): string
    {
        return number_format($number, 2);
    }

    private function getLeGuideGeneralSettingConfig($setting)
    {
        return $this->scopeConfig->getValue(
            self::LG_GENERAL_SETTINGS_CONFIGPATH . $setting,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    private function getEnabled()
    {
        return $this->getLeGuideGeneralSettingConfig('enabled');
    }

    private function getLeguideVATSetting()
    {
        return $this->getLeGuideGeneralSettingConfig('leguide_total_vat_setting') ?? 'include';
    }

    private function getLeguideProductIdValue()
    {
        return $this->getLeGuideGeneralSettingConfig('leguide_product_id_value') ?? 'sku';
    }
}