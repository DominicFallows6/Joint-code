<?php

namespace Limitless\Reports\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\ImportExport\Model\Export\Adapter\Csv;
use Magento\ImportExport\Model\Export\Adapter\CsvFactory;
use Magento\Store\Model\StoreManagerInterface;

class Cron
{
    const FILE_DIR = "limitless_reports/";
    const FILE_NAME = "category_position_%%";

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

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;


    public function __construct(
        CollectionFactory $productCollection,
        CategoryCollectionFactory $categoryCollection,
        CategoryFactory $categoryFactory,
        Visibility $catalogProductVisibility,
        StoreManagerInterface $storeManager,
        CsvFactory $csvFactory
    ) {
        $this->collection = $productCollection;
        $this->categoryCollection = $categoryCollection;
        $this->categoryFactory = $categoryFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->storeManager = $storeManager;
        $this->csvFactory = $csvFactory;
    }
    
    /**
     * @param $storeId
     * @return CollectionFactory
     */
    public function getProducts($storeId, $category)
    {
        $collection = $this->collection->create()->setStore($storeId)->addAttributeToSelect('*')->addCategoryFilter($category)->addAttributeToSort('position')->load();
        return $collection;
    }

    /**
     * @return mixed
     */
    public function getCategories($storeId)
    {
        $collection = $this->categoryCollection->create()->setStore($storeId)->addAttributeToSelect('*')->load();
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

    public function exportDefault()
    {
            $storeId = '0';
            $this->storeManager->setCurrentStore($storeId);

            $collection = $this->getCategories($storeId);

            $fileName = str_replace("%%", $storeId, self::FILE_NAME);
            $destination = self::FILE_DIR . DIRECTORY_SEPARATOR . $fileName . ".csv";
            $this->csv = $this->csvFactory->create(["destination" => $destination]);

            $this->writeToFile($collection, $storeId);
        
    }

    public function export()
    {
        $stores = $this->getStores();

        foreach ($stores as $store) {

            $storeId = $store->getId();
            $this->storeManager->setCurrentStore($storeId);

            $collection = $this->getCategories($storeId);

            $fileName = str_replace("%%", $storeId, self::FILE_NAME);
            $destination = self::FILE_DIR . DIRECTORY_SEPARATOR . $fileName . ".csv";
            $this->csv = $this->csvFactory->create(["destination" => $destination]);

            $this->writeToFile($collection, $storeId);
        }
    }

    /**
     * @param $collection
     */
    protected function writeToFile($collection, $storeId)
    {
        if (count($collection) > 0) {
            foreach ($collection as $category) {
                /** @var \Magento\Catalog\Model\Category $category */

                $products = $this->getProducts($storeId, $category);

                foreach ($products as $product) {
                    /** @var \Magento\Catalog\Model\Product $product */

                    $this->csv->writeRow(
                        [
                            'Product ID' => $product->getId(),
                            'SKU' => $product->getSku(),
                            'Category ID' => $category->getId(),
                            'Category Name' => $category->getName(),
                            'Position' => $product->getData('cat_index_position'),


                        ]);
                }
            }
            }
        }
    
}