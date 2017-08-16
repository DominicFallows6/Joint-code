<?php

namespace Limitless\ProductOOSSortOrder\Plugin;

use Limitless\ProductOOSSortOrder\Model\MoveProductsToEndOfCategory;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Model\StockManagement;

class MoveOOSProductsDownPlugin
{
    /** @var MoveProductsToEndOfCategory */
    private $moveProductsToEndOfCategory;

    public function __construct(MoveProductsToEndOfCategory $moveProductsToEndOfCategory)
    {
        $this->moveProductsToEndOfCategory = $moveProductsToEndOfCategory;
    }

    /**
     * @param StockManagement $subject
     * @param StockItemInterface[] $fullSaveItems
     * @return StockItemInterface[]
     */
    public function afterRegisterProductsSale(StockManagement $subject, $fullSaveItems)
    {
        //If items are set get details
        $noStockProducts = [];

        foreach ($fullSaveItems as $fullSaveItem)
        {
            $noStockProducts[] = $fullSaveItem->getProductId();
        }

        $this->moveProductsToEndOfCategory->processProducts($noStockProducts);

        return $fullSaveItems;
    }
}