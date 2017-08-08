<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Controller\Adminhtml\Step;

use Limitless\SortCategoryProducts\Model\ProcessStep;
use Limitless\SortCategoryProducts\Session\SortCategoryProductsSession;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page as BackendPage;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\View\Result\PageFactory;

class Process extends Action
{
    const ADMIN_RESOURCE = 'Limitless_SortCategoryProducts::batch_process';

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var SortCategoryProductsSession
     */
    private $session;

    /**
     * @var ProcessStep
     */
    private $processStep;

    /**
     * @var RedirectFactory
     */
    private $redirectFactory;

    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        SortCategoryProductsSession $session,
        ProcessStep $processStep
    ) {
        parent::__construct($context);
        $this->redirectFactory = $context->getResultRedirectFactory();
        $this->pageFactory = $pageFactory;
        $this->session = $session;
        $this->processStep = $processStep;
    }

    public function execute()
    {
        try {
            $startTime = microtime(true);
            $this->processStep->setCategoryProductPositions($this->getBatchData(), $this->getRowNumbersWithErrors());
            $processingTime = microtime(true) - $startTime;
            $this->getMessageManager()->addSuccessMessage($this->buildSuccessMessage($processingTime));
            return $this->redirectToInitialStepPage();
        } catch (\Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
            return $this->redirectToInitialStepPage();
        }
    }

    private function redirectToInitialStepPage(): Redirect
    {
        $redirect = $this->redirectFactory->create();
        $redirect->setPath('*/*/index');
        return $redirect;
    }
    private function getRowNumbersWithErrors(): array
    {
        return array_keys($this->getValidationErrorsFromSession());
    }

    private function getBatchData(): array
    {
        return (array) $this->session->getBatchData();
    }

    private function buildSuccessMessage(float $processingTime): string
    {
        $validRecordCount = count($this->getBatchData()) - count($this->getValidationErrorsFromSession());
        return (string) __(
            'Successfully applied %1 product positions in %2 seconds.',
            $validRecordCount,
            sprintf('%.2f', $processingTime)
        );
    }

    private function getValidationErrorsFromSession(): array
    {
        return (array) $this->session->getValidationErrors();
    }
}
