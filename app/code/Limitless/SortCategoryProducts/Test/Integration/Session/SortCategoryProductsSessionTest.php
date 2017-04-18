<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Session;

use Magento\Framework\Session\SessionManager;
use Magento\TestFramework\ObjectManager;

class SortCategoryProductsSessionTest extends \PHPUnit_Framework_TestCase
{
    private function createSortCategoryProductsSession(): SortCategoryProductsSession
    {
        return ObjectManager::getInstance()->create(SortCategoryProductsSession::class);
    }

    public function testExtendsSessionManager()
    {
        $this->assertInstanceOf(SessionManager::class, $this->createSortCategoryProductsSession());
    }

    public function testStoresBatchData()
    {
        $testBatchData = [[1, 'foo', 2]];

        $session = $this->createSortCategoryProductsSession();
        $session->setBatchData($testBatchData);
        $value = $session->getBatchData();
        $session->clearBatchData();
        $this->assertSame($testBatchData, $value);
        $this->assertNull($session->getBatchData());
    }

    public function testStoresValidationErrors()
    {
        $validationErrors = [1 => [['foo']], 100 => [['bar'],['baz']]];

        $session = $this->createSortCategoryProductsSession();
        $session->setValidationErrors($validationErrors);
        $value = $session->getValidationErrors();
        $session->clearValidationErrors();
        $this->assertSame($validationErrors, $value);
        $this->assertNull($session->getValidationErrors());
    }
}
