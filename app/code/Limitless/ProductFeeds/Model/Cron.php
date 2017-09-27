<?php

namespace Limitless\ProductFeeds\Model;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\ImportExport\Model\Export\Adapter\Csv;
use Magento\ImportExport\Model\Export\Adapter\CsvFactory;
use Magento\Store\Model\StoreManagerInterface;

class Cron
{
    const FILE_DIR = "feeds/";
    const FILE_NAME = "final_price_%%";
    
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CsvFactory
     */
    protected $csvFactory;
    /**
     * @var Csv
     */
    protected $csv;
    

    public function __construct(
        CollectionFactory $productCollection,
        StoreManagerInterface $storeManager,
        CsvFactory $csvFactory
    ) {
        $this->collection = $productCollection;
        $this->storeManager = $storeManager;
        $this->csvFactory = $csvFactory;
    }

    /**
     * @param $storeId
     * @return CollectionFactory
     */
    public function getProducts($storeId)
    {
        $collection = $this->collection->create()->setStore($storeId)->addAttributeToSelect('*')->load();
        return $collection;
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface[]
     */
    public function getStores()
    {
        $stores = $this->storeManager->getStores();
        return $stores;
    }

    public function export()
    {
        $stores = $this->getStores();
        
        foreach ($stores as $store) {
            
            $storeId = $store->getId();
            $this->storeManager->setCurrentStore($storeId);
            
            $collection = $this->getProducts($storeId);

            $fileName = str_replace("%%", $storeId, self::FILE_NAME);
            $destination = self::FILE_DIR . DIRECTORY_SEPARATOR . $fileName . ".csv";
            $this->csv = $this->csvFactory->create(["destination" => $destination]);
            
            $this->writeToFile($collection);
        }
    }


    /**
     * @param $collection
     */
    protected function writeToFile($collection)
    {
        if (count($collection) > 0) {
            foreach ($collection as $product) {
                /** @var \Magento\Catalog\Model\Product $product */
                $finalPrice = $product->getPriceInfo()->getPrice('final_price')->getValue();
                
                if($product->getStatus()=='1'){
                    $status = 'Enabled';
                } else {
                    $status = 'Disabled';
                }
                
                if ($finalPrice > 0) {
                    $this->csv->writeRow(
                        [
                            'sku' => $product->getSku(),
                            'final_price' => $finalPrice,
                            'store' => $product->getStore()->getName(),
                            'status' => $status,

                        ]);
                }
            }
        }
    }
}