<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ProcessStep;

use Limitless\SortCategoryProducts\Model\BatchDataFormatInterface;
use Magento\Catalog\Api\CategoryLinkRepositoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryProductLinkInterface;
use Magento\Catalog\Api\Data\CategoryProductLinkInterfaceFactory;

class ApplyBatchSorting
{
    const CATEGORY_COLUMN = BatchDataFormatInterface::CATEGORY_COLUMN;
    const SKU_COLUMN = BatchDataFormatInterface::SKU_COLUMN;
    const POSITION_COLUMN = BatchDataFormatInterface::POSITION_COLUMN;
    
    /**
     * @var CategoryProductLinkInterfaceFactory
     */
    private $categoryProductLinkFactory;

    /**
     * @var CategoryLinkRepositoryInterface
     */
    private $categoryLinkRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    public function __construct(
        CategoryProductLinkInterfaceFactory $categoryProductLinkFactory,
        CategoryLinkRepositoryInterface $categoryLinkRepository,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->categoryProductLinkFactory = $categoryProductLinkFactory;
        $this->categoryLinkRepository = $categoryLinkRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function apply(array $batchData)
    {
        array_map([$this, 'applyCategoryProductPosition'], $batchData);
    }

    private function applyCategoryProductPosition(array $categoryProductLinkData)
    {
        $categoryProductLink = $this->createCategoryProductLink($categoryProductLinkData);
        $this->unsetMemoizedValueOnCategoryBugWorkaround($categoryProductLink);
        $this->categoryLinkRepository->save($categoryProductLink);
    }

    private function createCategoryProductLink(array $categoryProductLinkData): CategoryProductLinkInterface
    {
        $categoryProductLink = $this->categoryProductLinkFactory->create();
        $categoryProductLink->setCategoryId($categoryProductLinkData[self::CATEGORY_COLUMN]);
        $categoryProductLink->setSku($categoryProductLinkData[self::SKU_COLUMN]);
        $categoryProductLink->setPosition($categoryProductLinkData[self::POSITION_COLUMN]);

        return $categoryProductLink;
    }

    private function unsetMemoizedValueOnCategoryBugWorkaround(CategoryProductLinkInterface $categoryProductLink)
    {
        // Core bug: the list of assigned products is memoized on category model
        // instances but not updated when a category is saved.
        $category = $this->categoryRepository->get($categoryProductLink->getCategoryId());
        $property = new \ReflectionProperty($category, '_data');
        $property->setAccessible(true);
        $data = $property->getValue($category);
        unset($data['products_position']);
        $property->setValue($category, $data);
    }
}
