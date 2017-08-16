<?php

namespace Limitless\ProductOOSSortOrder\Model;

use Limitless\ProductOOSSortOrder\Model\ResourceModel\CategoryProducts;
use Magento\Catalog\Model\CategoryRepository;

class MoveProductsToEndOfCategory
{
    /** @var CategoryProducts */
    private $categoryProducts;

    /** @var CategoryRepository */
    private $categoryRepository;

    public function __construct(CategoryProducts $categoryProducts, CategoryRepository $categoryRepository)
    {
        $this->categoryProducts = $categoryProducts;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param int[] $productIds
     */
    public function processProducts(array $productIds)
    {
        $categoryIds = $this->categoryProducts->getCategoryIdsForProductIds($productIds);

        foreach ($categoryIds as $categoryId)
        {
            $category = $this->categoryRepository->get($categoryId);
            $productPositions = $category->getProductsPosition();

            $maxProductPosition = max($productPositions) + 1;

            foreach ($productIds as $productId)
            {
                if (isset($productPositions[$productId]))
                {
                    $productPositions[$productId] = "$maxProductPosition";
                }
            }

            $category->setPostedProducts($productPositions);

            try {
                $category->save();
            } catch (\Exception $e) {
                throw new CouldNotSaveException(__('Could not save product to the category'), $e);
            }
        }
    }
}