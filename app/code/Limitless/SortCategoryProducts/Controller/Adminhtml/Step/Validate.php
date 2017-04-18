<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Controller\Adminhtml\Step;

use Limitless\SortCategoryProducts\Block\Adminhtml\InitialStep\Form;
use Limitless\SortCategoryProducts\Model\ValidationStepFactory;
use Limitless\SortCategoryProducts\Session\SortCategoryProductsSession;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page as BackendPage;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\View\Result\PageFactory;

class Validate extends Action
{
    const ADMIN_RESOURCE = 'Limitless_SortCategoryProducts::batch_process';

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var ValidationStepFactory
     */
    private $validationStepFactory;

    /**
     * @var RedirectFactory
     */
    private $redirectFactory;

    /**
     * @var SortCategoryProductsSession
     */
    private $sortCategoryProductsSession;

    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        ValidationStepFactory $validationStepFactory,
        SortCategoryProductsSession $sortCategoryProductsSession
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->validationStepFactory = $validationStepFactory;
        $this->redirectFactory = $context->getResultRedirectFactory();
        $this->sortCategoryProductsSession = $sortCategoryProductsSession;
    }

    public function execute()
    {
        try {
            $this->validateBatchImportData();
            return $this->showValidationStepPage();
        } catch (\RuntimeException $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
            return $this->redirectToInitialStepPage();
        }
    }

    private function validateBatchImportData()
    {
        $validationStep = $this->validationStepFactory->create(['fileName' => $this->getUploadedBatchFileName()]);
        
        $this->sortCategoryProductsSession->setBatchData($validationStep->getImportData());
        $this->sortCategoryProductsSession->setValidationErrors($validationStep->getErrorMessages());
    }

    private function redirectToInitialStepPage(): Redirect
    {
        $redirect = $this->redirectFactory->create();
        $redirect->setPath('*/*/index');
        
        return $redirect;
    }

    private function showValidationStepPage(): BackendPage
    {
        /** @var BackendPage $page */
        $page = $this->pageFactory->create();
        $page->setActiveMenu('Limitless_SortCategoryProducts::batch_process');
        $page->getConfig()->getTitle()->prepend('Validate Update Category Product Positions Batch Data');

        return $page;
    }

    private function getUploadedBatchFileName(): string
    {
        if (!$_FILES || !isset($_FILES[Form::FILE_PARAM])) {
            throw new \RuntimeException('No category products sort order batch file uploaded.');
        }

        return (string) @$_FILES[Form::FILE_PARAM]['tmp_name'];
    }
}
