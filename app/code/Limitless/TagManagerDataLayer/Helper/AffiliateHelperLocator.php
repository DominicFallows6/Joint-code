<?php

namespace Limitless\TagManagerDataLayer\Helper;

use Magento\Framework\ObjectManagerInterface;
use Limitless\TagManagerDataLayer\Api\AffiliateHelperInterface;

class AffiliateHelperLocator
{
    /** @var ObjectManagerInterface */
    private $objectManager;

    /** @var string[] */
    private $affiliateHelperPool;

    public function __construct(ObjectManagerInterface $objectManager, array $affiliateHelperPool = [])
    {
        $this->objectManager = $objectManager;
        $this->affiliateHelperPool = $affiliateHelperPool;
    }

    /**
     * @param string $affiliateCode
     * @return AffiliateHelperInterface
     */
    public function locate($affiliateCode): AffiliateHelperInterface
    {
        if (! is_string($affiliateCode) || strlen($affiliateCode) < 3) {
            $message = sprintf('The affiliate code is invalid: "%s"', $affiliateCode ?? 'NULL');
            throw new \InvalidArgumentException($message);
        }
        if (! isset($this->affiliateHelperPool[$affiliateCode])) {
            throw new \InvalidArgumentException(sprintf('No helper registered for affiliate code "%s"', $affiliateCode));
        }
        return $this->objectManager->get($this->affiliateHelperPool[$affiliateCode]);
    }

    public function isValid(string $affiliateCode)
    {
        return isset($this->affiliateHelperPool[$affiliateCode]);
    }
}