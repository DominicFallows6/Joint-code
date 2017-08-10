<?php

namespace Limitless\TrustpilotApi\Helper;

use Limitless\TrustpilotApi\Block\TrustpilotApi;
use Limitless\TrustpilotApi\Model\TrustpilotCache;
use Limitless\TrustpilotApi\Model\ResourceModel\TrustpilotCache as TrustpilotCacheResource;
use Limitless\TrustpilotApi\Model\TrustpilotCacheFactory;
use Magento\Store\Model\StoreRepository;

class TrustpilotApiRestService
{
    /** @var TrustpilotApi */
    private $trustpilotApi;

    /** @var TrustpilotCacheFactory */
    private $trustpilotCacheFactory;

    /** @var StoreRepository */
    private $storeRepository;

    public function __construct(
        StoreRepository $storeRepository,
        TrustpilotApi $trustpilotApi,
        TrustpilotCacheFactory $trustpilotCacheFactory
    ) {
        $this->trustpilotApi = $trustpilotApi;
        $this->trustpilotCacheFactory = $trustpilotCacheFactory;
        $this->storeRepository = $storeRepository;
    }

    public function populateTrustpilotCacheData()
    {
        foreach($this->getAllStores() as $store)
        {
            $storeId = (int) $store->getId();

            //Update for all not just ones with Cache enabled but not if disabled
            if (!empty($this->trustpilotApi->getTrustpilotApiKey($storeId))
                && !empty($this->trustpilotApi->getTrustpilotBusinessId($storeId))
                && $this->trustpilotApi->getTrustpilotApiEnabled($storeId)
            ) {
                $this->initTrustpilotURLData($storeId);
            }
        }
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface[]
     */
    private function getAllStores(): array
    {
        return $this->storeRepository->getList();
    }

    private function initTrustpilotURLData(int $storeId)
    {
        $data = [];
        $data['storeid'] = $storeId;

        $trustRestUrls = $this->getTrustRestUrls($storeId);

        foreach ($trustRestUrls as $key => $url) {
            $data[$key] = $this->trustpilotCurl($url, $storeId);
        }

        $this->saveTrustpilotCacheData($data);
    }

    private function getTrustRestUrls(int $storeId): array
    {
        $urls = [];
        $urls['business_data'] = $this->trustpilotApi->getTrustpilotApiBusinessURLClean($storeId);
        $urls['review_data'] = $this->trustpilotApi->getTrustpilotApiReviewsURLClean($storeId);

        return $urls;
    }

    private function trustpilotCurl(string $url, int $storeId): string
    {
        $ch = curl_init();

        curl_setopt(
            $ch,CURLOPT_HTTPHEADER,
            ['apikey: ' . $this->trustpilotApi->getTrustpilotApiKey($storeId)]
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);
        curl_close($ch);

        return (string) $output;
    }

    private function saveTrustpilotCacheData(array $trustpilotData)
    {
        $saveTime = time();

        /** @var TrustpilotCache $trustpilotCacheModel */
        $trustpilotCacheModel = $this->trustpilotCacheFactory->create();

        try {
            $trustpilotCacheModel->getResource()->load(
                $trustpilotCacheModel,
                $trustpilotData['storeid'],
                TrustpilotCache::STORE_CODE
            );
        } catch (\Exception $e) {
            //No current store -> continue to save
        }

        $trustpilotCacheModel->setData(TrustpilotCache::STORE_CODE, $trustpilotData['storeid']);
        $trustpilotCacheModel->setData(TrustpilotCache::BUSINESS_UNITS_CACHE, $trustpilotData['business_data']);
        $trustpilotCacheModel->setData(TrustpilotCache::REVIEW_CACHE, $trustpilotData['review_data']);
        $trustpilotCacheModel->setData(TrustpilotCache::DATE_CACHE_UPDATED, $saveTime);

        $trustpilotCacheModel->getResource()->save($trustpilotCacheModel);
        $trustpilotCacheModel = null;
    }
}