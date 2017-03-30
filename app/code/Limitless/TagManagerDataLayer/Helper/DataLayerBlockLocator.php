<?php

namespace Limitless\TagManagerDataLayer\Helper;

use Magento\Framework\ObjectManagerInterface;

class DataLayerBlockLocator
{
    /** @var ObjectManagerInterface */
    private $objectManager;

    /** @var string[] */
    public $contentBlockPool;

    public function __construct(ObjectManagerInterface $objectManager, array $contentBlockPool = [])
    {
        $this->objectManager = $objectManager;
        $this->contentBlockPool = $contentBlockPool;
    }

    public function locate($contentCode)
    {
        if (! is_string($contentCode) || strlen($contentCode) < 3) {
            $message = sprintf('The code is invalid: "%s"', $contentCode ?? 'NULL');
            throw new \InvalidArgumentException($message);
        }
        if (! isset($this->contentBlockPool[$contentCode])) {
            throw new \InvalidArgumentException(sprintf('No block registered for code "%s"', $contentCode));
        }
        return $this->objectManager->get($this->contentBlockPool[$contentCode]);
    }

    public function isValid(string $contentCode)
    {
        return isset($this->contentBlockPool[$contentCode]);
    }
}