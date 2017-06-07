<?php

declare(strict_types=1);

namespace Limitless\ProductApiPerformance\Catalog;

use Magento\Catalog\Api\Data\ProductLinkInterface;
use Magento\Catalog\Api\ProductLinkRepositoryInterface;
use Magento\Catalog\Model\Product\Link\SaveHandler;
use Magento\Catalog\Model\ResourceModel\Product\Link;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Extending the original class only so if there are any
 * declared plugins they are applied.
 */
class ProductLinkSaveHandler extends SaveHandler
{
    /**
     * @var ProductLinkRepositoryInterface
     */
    protected $productLinkRepository;

    /**
     * @var ExtensibleDataObjectConverter
     */
    private $extensibleDataObjectConverter;

    /**
     * @var array[]
     */
    private $memoizedProductLinkArrayRepresentations = [];

    /**
     * @param MetadataPool $metadataPool
     * @param Link $linkResource
     * @param ProductLinkRepositoryInterface $productLinkRepository
     */
    public function __construct(
        MetadataPool $metadataPool,
        Link $linkResource,
        ProductLinkRepositoryInterface $productLinkRepository,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->productLinkRepository = $productLinkRepository;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;

        parent::__construct($metadataPool, $linkResource, $productLinkRepository);
    }

    /**
     * @param string $entityType
     * @param \Magento\Catalog\Api\Data\ProductInterface $entity
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entityType, $entity)
    {
        $existingLinks = (array) $this->productLinkRepository->getList($entity);
        $newLinks = (array) $entity->getProductLinks();

        $linksToRemove = $this->getLinksNotInFirstList($newLinks, $existingLinks);
        $linksToSave = $this->getLinksNotInFirstList($existingLinks, $newLinks);

        array_map([$this->productLinkRepository, 'delete'], $linksToRemove);
        array_map([$this->productLinkRepository, 'save'], $linksToSave);

        return $entity;
    }

    /**
     * @param ProductLinkInterface[] $productLinks
     * @param ProductLinkInterface[] $otherProductLinks
     * @return ProductLinkInterface[]
     */
    private function getLinksNotInFirstList(array $productLinks, array $otherProductLinks): array
    {
        $linksNotInFirstList = [];
        foreach ($otherProductLinks as $otherLink) {
            if (! $this->arrayContainsLink($productLinks, $otherLink)) {
                $linksNotInFirstList[] = $otherLink;
            }
        }
        return $linksNotInFirstList;
    }

    /**
     * @param ProductLinkInterface[] $productLinks
     * @param ProductLinkInterface $productLink
     * @return bool
     */
    private function arrayContainsLink(array $productLinks, ProductLinkInterface $productLink): bool
    {
        foreach ($productLinks as $potentiallyMatchingLink) {
            if ($this->isSameLink($productLink, $potentiallyMatchingLink)) {
                return true;
            }
        }
        return false;
    }

    private function isSameLink(ProductLinkInterface $linkA, ProductLinkInterface $linkB): bool
    {
        $a = $this->getProductLinkInstanceAsArray($linkA);
        $b = $this->getProductLinkInstanceAsArray($linkB);
        return $this->isSameArray($a, $b);
    }

    private function getProductLinkInstanceAsArray(ProductLinkInterface $productLink): array
    {
        $hash = spl_object_hash($productLink);
        if (! isset($this->memoizedProductLinkArrayRepresentations[$hash])) {
            $this->memoizedProductLinkArrayRepresentations[$hash] = $this->convertProductLinkToArray($productLink);
        }
        return $this->memoizedProductLinkArrayRepresentations[$hash];
    }

    private function convertProductLinkToArray(ProductLinkInterface $productLink): array
    {
        return $this->extensibleDataObjectConverter->toNestedArray($productLink, [], ProductLinkInterface::class);
    }

    /**
     * @param mixed[] $a
     * @param mixed[] $b
     * @return bool
     */
    private function isSameArray(array $a, array $b): bool
    {
        $keysA = array_keys($a);
        $keysB = array_keys($b);
        if (array_diff($keysA, $keysB) || array_diff($keysB, $keysA)) {
            return false;
        }
        foreach ($keysA as $key) {
            if (! $this->isSameValue($a[$key], $b[$key])) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @return bool
     */
    private function isSameValue($a, $b): bool
    {
        if (is_array($a) && is_array($b)) {
            return $this->isSameArray($a, $b);
        }
        return $a === $b;
    }
}
