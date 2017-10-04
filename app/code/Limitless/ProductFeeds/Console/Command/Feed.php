<?php

namespace Limitless\ProductFeeds\Console\Command;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\State as AppState;
use Magento\ImportExport\Model\Export\Adapter\Csv;
use Magento\ImportExport\Model\Export\Adapter\CsvFactory;
use Magento\Store\Model\StoreManagerInterface;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Feed extends Command
{
    const FILE_DIR = "limitless_feeds/";
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
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var Csv
     */
    protected $csv;

    /**
     * @var AppState
     */
    protected $state;

    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        StoreManagerInterface $storeManager,
        CsvFactory $csvFactory,
        AppState $state
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
        $this->csvFactory = $csvFactory;
        $this->state = $state;
        parent::__construct();


    }

    /**
     * @param $storeId
     * @return ProductCollectionFactory
     */
    public function getProducts($storeId)
    {
        $collection = $this->productCollectionFactory->create()->setStore($storeId)->addAttributeToSelect('*')->load();
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

    /**
     * @param $collection
     */
    protected function writeToFile($collection)
    {
        if (count($collection) > 0) {
            foreach ($collection as $product) {
                /** @var \Magento\Catalog\Model\Product $product */
                $finalPrice = $product->getPriceInfo()->getPrice('final_price')->getValue();

                if ($finalPrice > 0) {
                    $this->csv->writeRow(
                        [
                            'sku' => $product->getSku(),
                            'final_price' => $finalPrice,
                            'store' => $product->getStore()->getName(),
                            'status' => $product->getAttributeText('status'),
                            'visibility' => $product->getAttributeText('visibility'),

                        ]);
                }
            }
        }
    }

    protected function configure()
    {
        $this->setName('feed:export')
            ->setDescription('Export of Feeds');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);

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
    
}