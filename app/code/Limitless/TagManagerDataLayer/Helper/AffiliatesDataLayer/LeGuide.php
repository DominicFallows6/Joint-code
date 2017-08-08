<?php

namespace Limitless\TagManagerDataLayer\Helper\AffiliatesDataLayer;

use Limitless\TagManagerDataLayer\Api\AffiliateHelperInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class LeGuide implements AffiliateHelperInterface
{
    const LG_GENERAL_SETTINGS_CONFIGPATH = 'google/limitless_tagmanager_datalayer/affiliate_tracking/leguide/';

    const LG_DATALAYER_NAME = 'leguide';

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    private $scopeConfig;

    /** @var array */
    private $leGuideProductSkus;

    /** @var array */
    private $leGuideProductPrices;

    /** @var array */
    private $leGuideProductQtys;

    /** @var Session */
    private $checkoutSession;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var OrderInterface */
    private $lastOrder;

    public function __construct(
        Session $checkoutSession,
        OrderRepositoryInterface $orderRepository,
        Context $context
    ){
        $this->scopeConfig = $context->getScopeConfig();
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
    }

    /** @return mixed[] */
    public function getAffiliateDataLayer(): array
    {
        $this->initLastOrder();
        $orderItems = $this->lastOrder->getItems();
        $this->buildLeGuideValues($orderItems);

        return [self::LG_DATALAYER_NAME =>
            [
                'productlistSkus' => $this->leGuideProductSkus,
                'productlistPrices' => $this->leGuideProductPrices,
                'productlistQtys' => $this->leGuideProductQtys
            ]
        ];
    }

    private function initLastOrder()
    {
        $orderId = $this->checkoutSession->getData('last_order_id');
        $this->lastOrder =  $this->orderRepository->get($orderId);
    }

    public function buildLeGuideValues($orderItems)
    {
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

    private function ukNumberFormat($number)
    {
        if (is_numeric($number)) {
            return number_format($number, 2, '.', '');
        }
        return '';
    }

    private function getLeGuideGeneralSettingConfig($setting)
    {
        return $this->scopeConfig->getValue(
            self::LG_GENERAL_SETTINGS_CONFIGPATH . $setting,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
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