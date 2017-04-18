<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Test\Integration;

use Limitless\SortCategoryProducts\Model\ProcessStep;
use Limitless\SortCategoryProducts\Model\ValidationStep;
use Limitless\SortCategoryProducts\Test\FileFixtureTrait;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\Data\CategoryProductLinkInterface;
use Magento\TestFramework\ObjectManager;

/**
 * @magentoDataFixture Magento/Catalog/_files/categories.php
 */
class BatchCategoryProductPositionEdgeToEdgeTest extends \PHPUnit_Framework_TestCase
{
    use FileFixtureTrait;
    
    /**
     * @param int $categoryId
     * @return CategoryProductLinkInterface[]
     */
    private function getProductsAssignedToCategory(int $categoryId): array
    {
        /** @var CategoryLinkManagementInterface $categoryLinkManagement */
        $categoryLinkManagement = ObjectManager::getInstance()->create(CategoryLinkManagementInterface::class);

        return $categoryLinkManagement->getAssignedProducts($categoryId);
    }

    private function findBySkuAndCategoryId(array $unsorted, string $sku, $categoryId)
    {
        /** @var CategoryProductLinkInterface $productLink */
        foreach ($unsorted as $productLink) {
            if ($productLink->getSku() === $sku && $categoryId == $productLink->getCategoryId()) {
                return $productLink;
            }
        }
        $this->fail(sprintf('Unable product "%s" and category %d assigned in product link array', $sku, $categoryId));
    }

    private function buildBatchDataFixtureContent(int $fixtureCategoryId, array $assignedProducts): string
    {
        $counter = 0;
        $batchDataFileContent = implode("\n", array_map(
            function (CategoryProductLinkInterface $assignedProduct) use ($fixtureCategoryId, &$counter) {
                return sprintf('%d,"%s",%d', $fixtureCategoryId, $assignedProduct->getSku(), ++$counter);
            },
            $assignedProducts
        ));

        return $batchDataFileContent;
    }

    public function testSortCategoryProductsProcess()
    {
        $fixtureCategoryId = 4;
        
        $assignedProducts = $this->getProductsAssignedToCategory($fixtureCategoryId);

        $batchDataFileContent = $this->buildBatchDataFixtureContent($fixtureCategoryId, $assignedProducts);

        $fileName = $this->makeTempFile($batchDataFileContent);

        /** @var ValidationStep $validationStep */
        $validationStep = ObjectManager::getInstance()->create(ValidationStep::class, ['fileName' => $fileName]);
        /** @var ProcessStep $processStep */
        $processStep = ObjectManager::getInstance()->create(ProcessStep::class);

        $rowNumbersWithErrors = array_keys($validationStep->getErrorMessages());
        $processStep->setCategoryProductPositions($validationStep->getImportData(), $rowNumbersWithErrors);

        $newAssignedProducts = $this->getProductsAssignedToCategory($fixtureCategoryId);
        
        for ($i = 0, $max = count($assignedProducts); $i < $max; $i++) {
            $previousLink = $assignedProducts[$i];
            $newLink = $this->findBySkuAndCategoryId($newAssignedProducts, $previousLink->getSku(), $previousLink->getCategoryId());
            $this->assertSame($i + 1, (int) $newLink->getPosition());
        }
    }
}
