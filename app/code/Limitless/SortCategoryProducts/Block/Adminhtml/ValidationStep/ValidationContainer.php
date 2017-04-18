<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Block\Adminhtml\ValidationStep;

use Limitless\SortCategoryProducts\Session\SortCategoryProductsSession;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container as AbstractFormContainer;

class ValidationContainer extends AbstractFormContainer
{
    /**
     * @var SortCategoryProductsSession
     */
    private $session;

    public function __construct(
        Context $context,
        SortCategoryProductsSession $session,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->session = $session;
    }

    protected function _prepareLayout()
    {
        $this->addChild('form', __NAMESPACE__ . '\\Validation');
        $this->removeButton('reset');
        $this->removeButton('save');
        $this->removeButton('delete');

        if ($this->canContinueToNextStep()) {
            $this->addButton('save', [
                'label'          => __('Continue'),
                'class'          => 'save primary',
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/process') . '\')'
            ], 1);
        }

        return parent::_prepareLayout();
    }
    
    private function canContinueToNextStep()
    {
        $errors = $this->session->getValidationErrors();
        $batchImportData = $this->session->getBatchData();
        return count($batchImportData) > 0 && count($errors) < count($batchImportData);
    }
}
