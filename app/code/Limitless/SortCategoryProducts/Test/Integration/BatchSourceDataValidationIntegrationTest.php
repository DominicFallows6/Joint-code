<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Test\Integration;

use Limitless\SortCategoryProducts\Model\ValidationStep\Validation\ValidateBatchData;
use Magento\TestFramework\ObjectManager;

class BatchSourceDataValidationIntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     */
    public function testBatchDataValidationProducesExpectedErrors()
    {
        /** @var ValidateBatchData $validator */
        $validator = ObjectManager::getInstance()->create(ValidateBatchData::class);

        $this->assertValidatorReturnsNoErrorsForValidData($validator);
        $this->assertValidatorReturnsErrorsForInvalidData($validator);
    }

    private function assertValidatorReturnsErrorsForInvalidData(ValidateBatchData $validator)
    {
        $invalidCategoryId = [9999, 'simple', 10];
        $invalidPosition = [1, 'simple', 'foo'];
        $invalidSku = [5, '00000', 10];
        $skuNotAssignedToCategory = [10, 'simple', 20];
        $badCount = [10, 'simple-4'];

        $invalidBatchData = [
            0 => $invalidCategoryId,
            1 => $invalidSku,
            2 => $invalidPosition,
            3 => $skuNotAssignedToCategory,
            4 => $badCount
        ];
        $validationErrors = $validator->getValidationErrors($invalidBatchData);
        $this->assertCount(5, $validationErrors, "Too few or too many errors found in invalid fixture data.");
    }

    private function assertValidatorReturnsNoErrorsForValidData(ValidateBatchData $validator)
    {
        $validBatchData = [
            [2, 'simple', 10],
            [5, '12345', 10],
            [10, 'simple-4', 20],
        ];
        $this->assertSame([], $validator->getValidationErrors($validBatchData));
    }
}
