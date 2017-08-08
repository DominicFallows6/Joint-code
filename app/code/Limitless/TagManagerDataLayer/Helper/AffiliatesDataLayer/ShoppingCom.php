<?php

namespace Limitless\TagManagerDataLayer\Helper\AffiliatesDataLayer;

use Limitless\TagManagerDataLayer\Api\AffiliateHelperInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\View\Element\Template\Context;

class ShoppingCom implements AffiliateHelperInterface
{
    const SHOPPINGCOM_GENERAL_SETTINGS_CONFIGPATH = 'google/limitless_tagmanager_datalayer/affiliate_tracking/shopping_com/';

    const SHOPPINGCOM_DATALAYER_NAME = 'shoppingcom';

    /** @var array */
    private $shoppingComProductSkus;

    /** @var array */
    private $shoppingComProductNames;

    /** @var array */
    private $shoppingComProductCatIds;

    /** @var array */
    private $shoppingComProductCatNames;

    /** @var Session */
    protected $checkoutSession;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var OrderInterface */
    private $lastOrder;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var CategoryRepositoryInterface */
    private $categoryRepository;

    public function __construct(
        Session $checkoutSession,
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
        Context $context,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->scopeConfig = $context->getScopeConfig();
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return array
     */
    public function getAffiliateDataLayer(): array
    {
        $this->initLastOrder();
        $orderItems = $this->lastOrder->getItems();
        $this->buildShoppingComValues($orderItems);

        return [self::SHOPPINGCOM_DATALAYER_NAME =>
            [
                'ordervalue' => $this->getShoppingComOrderTotal(),
                'productids' => $this->shoppingComProductSkus,
                'productnames' => $this->shoppingComProductNames,
                'productcatids' => $this->shoppingComProductCatIds,
                'productcatnames' => $this->shoppingComProductCatNames
            ]
        ];
    }

    private function initLastOrder()
    {
        $orderId = $this->checkoutSession->getData('last_order_id');
        $this->lastOrder = $this->orderRepository->get($orderId);
    }

    public function buildShoppingComValues($orderItems)
    {
        $delimiter = ',';

        $productSkus = $productName = $productCatId = $productCatName = [];

        foreach ($orderItems as $productItem) {

            $itemCode = $this->getShoppingComItemCode($productItem);

            $productSkus[] = htmlspecialchars($itemCode);
            $productName[] = htmlspecialchars($productItem->getName());

            //TODO: possibly get an attribute for this.
            $prodRepo = $this->productRepository->get($productItem->getSku());
            $categoryIds = $prodRepo->getCategoryIds();

            if (isset($categoryIds[0])) {
                $category = $this->categoryRepository->get($categoryIds[0]);

                $productCatId[] = $categoryIds[0];
                $productCatName[] = htmlspecialchars($category->getName());
            }
        }

        $this->shoppingComProductSkus = '\\'.implode($delimiter, $productSkus);
        $this->shoppingComProductNames = '\\'.implode($delimiter, $productName);
        $this->shoppingComProductCatIds = '\\'.implode($delimiter, $productCatId);
        $this->shoppingComProductCatNames = '\\'.implode($delimiter, $productCatName);
    }

    /**
     * @param $orderItem
     * @return string
     */
    private function getShoppingComItemCode($productItem): string
    {
        $productIdValueSetting = $this->getShoppingComProductIdValue();

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
     * @param $productItem
     * @return string
     */
    private function getShoppingComOrderTotal(): string
    {
        $vatSetting = $this->getShoppingComVATSetting();
        $shippingSetting = $this->getShoppingComShippingSetting();
        $orderTotal = $this->lastOrder->getGrandTotal();

        switch ($vatSetting) {
            case 'exclude':
                $orderTotal -= $this->lastOrder->getTaxAmount();
                break;
        }

        switch ($shippingSetting) {
            case 'exclude':
                $orderTotal -= $this->lastOrder->getShippingAmount();
                break;
        }

        return $this->ukNumberFormat($orderTotal);
    }

    private function ukNumberFormat($number)
    {
        if (is_numeric($number)) {
            return number_format($number, 2, '.', '');
        }
        return '';
    }

    private function getShoppingComGeneralSettingConfig($setting)
    {
        return $this->scopeConfig->getValue(
            self::SHOPPINGCOM_GENERAL_SETTINGS_CONFIGPATH . $setting,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    private function getShoppingComProductIdValue()
    {
        return $this->getShoppingComGeneralSettingConfig('shoppingcom_product_id_value') ?? 'sku';
    }

    private function getShoppingComVATSetting()
    {
        return $this->getShoppingComGeneralSettingConfig('shoppingcom_total_vat_setting') ?? 'include';
    }

    private function getShoppingComShippingSetting()
    {
        return $this->getShoppingComGeneralSettingConfig('shoppingcom_total_shipping_setting') ?? 'include';
    }
}
