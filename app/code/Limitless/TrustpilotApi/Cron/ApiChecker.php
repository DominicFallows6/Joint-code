<?php

namespace Limitless\TrustpilotApi\Cron;

use Limitless\TrustpilotApi\Helper\TrustpilotApiRestService;

class ApiChecker
{
    /** @var TrustpilotApiRestService */
    private $apiRestService;

    public function __construct(TrustpilotApiRestService $apiRestService) {
        $this->apiRestService = $apiRestService;
    }

    public function execute()
    {
        $this->apiRestService->populateTrustpilotCacheData();
    }
}