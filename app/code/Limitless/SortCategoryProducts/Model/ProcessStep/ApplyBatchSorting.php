<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ProcessStep;

use Limitless\SortCategoryProducts\Model\BatchDataFormatInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResource;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\Exception\CouldNotSaveException;

class ApplyBatchSorting
{
    const CATEGORY_COLUMN = BatchDataFormatInterface::CATEGORY_COLUMN;
    const SKU_COLUMN = BatchDataFormatInterface::SKU_COLUMN;
    const POSITION_COLUMN = BatchDataFormatInterface::POSITION_COLUMN;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @var CategoryResource
     */
    private $categoryResource;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        ProductResource $productResource,
        CategoryResource $categoryResource
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->productResource = $productResource;
        $this->categoryResource = $categoryResource;
    }

    public function apply(array $batchData)
    {
        $skuToProductIdMap = $this->productResource->getProductsIdsBySkus(array_column($batchData, self::SKU_COLUMN));
        array_map(function (array $categoryProductPositions) use ($skuToProductIdMap) {
            $this->applyCategoryProductPositions($categoryProductPositions, $skuToProductIdMap);
        }, $this->groupByCategory($batchData));
    }

    private function groupByCategory(array $batchData): array
    {
        return array_reduce($batchData, function (array $acc, array $row) {
            $acc[$row[self::CATEGORY_COLUMN]][] = $row;

            return $acc;
        }, []);
    }

    /**
     * The original core code which handles one category link at a time:
     * @see \Magento\Catalog\Model\CategoryLinkRepository::save()
     */
    private function applyCategoryProductPositions(array $linksData, array $skuToProductIdMap)
    {
        $categoryId = reset($linksData)[self::CATEGORY_COLUMN];
        $category = $this->getCategoryWithUpdatedLinks($categoryId, $linksData, $skuToProductIdMap);

        try {
            $this->categoryResource->save($category);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('Could not save product positions to category %1, (%2)', $categoryId, $e->getMessage())
            );
        }
    }

    private function getCategoryWithUpdatedLinks($categoryId, array $linksData, array $skuToProductIdMap): Category
    {
        $category = $this->getCategoryModelById($categoryId);

        $newProductPositions = $this->buildProductIdToPositionMap($linksData, $skuToProductIdMap);
        $oldProductPositions = $category->getProductsPosition();
        $merged = array_replace($oldProductPositions, $newProductPositions);
        $category->setData('posted_products', $merged);

        return $category;
    }

    private function buildProductIdToPositionMap(array $linksData, array $skuToProductIdMap): array
    {
        return array_reduce($linksData, function (array $map, array $row) use ($skuToProductIdMap) {
            $sku = $row[self::SKU_COLUMN];
            $map[$skuToProductIdMap[$sku]] = $row[self::POSITION_COLUMN];

            return $map;
        }, []);
    }

    /**
     * @param int $categoryId
     * @return Category|CategoryInterface
     */
    private function getCategoryModelById($categoryId): Category
    {
        /** @var Category|CategoryInterface $category */
        $category = $this->categoryRepository->get($categoryId);
        $this->unsetMemoizedValueOnCategoryBugWorkaround($category);

        return $category;
    }

    private function unsetMemoizedValueOnCategoryBugWorkaround(Category $category)
    {
        // Core bug: the list of assigned products is memoized on category model
        // instances but not updated when a category is saved.
        $property = new \ReflectionProperty($category, '_data');
        $property->setAccessible(true);
        $data = $property->getValue($category);
        unset($data['products_position']);
        $property->setValue($category, $data);
    }
}
