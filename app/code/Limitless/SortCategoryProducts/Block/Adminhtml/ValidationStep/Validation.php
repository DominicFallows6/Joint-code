<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Block\Adminhtml\ValidationStep;

use Limitless\SortCategoryProducts\Session\SortCategoryProductsSession;
use Magento\Framework\View\Element\Template;

class Validation extends Template
{
    /**
     * @var SortCategoryProductsSession
     */
    private $session;

    public function __construct(
        Template\Context $context,
        SortCategoryProductsSession $session,
        $template = 'Limitless_SortCategoryProducts::validation.phtml',
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->session = $session;
        $this->setTemplate($template);
    }

    public function hasValidationErrors()
    {
        return $this->getNumberOfInvalidRecords() > 0;
    }

    public function canContinueToProcessStep()
    {
        return $this->getNumberOfValidRecords() > 0;
    }

    public function getNumberOfValidRecords()
    {
        return $this->getNumberOfBatchImportRecords() - $this->getNumberOfInvalidRecords();
    }

    public function getNumberOfInvalidRecords()
    {
        return count($this->getValidationErrorsFromSession());
    }

    private function getNumberOfBatchImportRecords(): int
    {
        return count((array) $this->session->getBatchData());
    }

    public function getValidationErrors()
    {
        $validationErrorsFromSession = $this->getValidationErrorsFromSession();
        $recordsWithErrorsIndexes = array_keys($validationErrorsFromSession);
        sort($recordsWithErrorsIndexes);
        foreach ($recordsWithErrorsIndexes as $idx) {
            $rowNumber = $idx + 1;
            yield $rowNumber => $this->renderErrorMessages($validationErrorsFromSession[$idx]);
        }
    }

    private function renderErrorMessages(array $errorsInRow)
    {
        return array_map(function (array $errorMessageParts) {
            return (string) __(...$errorMessageParts);
        }, $errorsInRow);
    }

    /**
     * @return array[]
     */
    private function getValidationErrorsFromSession(): array
    {
        return (array) $this->session->getValidationErrors();
    }
}
