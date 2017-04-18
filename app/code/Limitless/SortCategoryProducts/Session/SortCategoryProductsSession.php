<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Session;

use Magento\Framework\Session\SessionManager;

class SortCategoryProductsSession extends SessionManager
{
    public function setBatchData(array $batchData = null)
    {
        $this->setSortCategoryProductsBatchData($batchData);
    }

    /**
     * @return array|null
     */
    public function getBatchData()
    {
        return $this->getSortCategoryProductsBatchData();
    }

    public function clearBatchData()
    {
        $this->setSortCategoryProductsBatchData(null);
    }

    public function setValidationErrors(array $validationErrors)
    {
        $this->setSortCategoryProductsValidationErrors($validationErrors);
    }

    /**
     * @return array|null
     */
    public function getValidationErrors()
    {
        return $this->getSortCategoryProductsValidationErrors();
    }

    public function clearValidationErrors()
    {
        $this->unsSortCategoryProductsValidationErrors();
    }
}
