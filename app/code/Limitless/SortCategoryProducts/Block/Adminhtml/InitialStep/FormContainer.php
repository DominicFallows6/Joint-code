<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Block\Adminhtml\InitialStep;

use Magento\Backend\Block\Widget\Form\Container as AbstractFormContainer;

class FormContainer extends AbstractFormContainer
{
    protected function _prepareLayout()
    {
        $this->addChild('form', __NAMESPACE__ . '\\Form');
        $this->removeButton('reset');
        $this->removeButton('back');
        $this->removeButton('save');
        $this->removeButton('delete');
        $this->addButton('save', [
            'label'          => __('Continue'),
            'class'          => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save', 'target' => '#edit_form']],
            ],
        ], 1);

        return parent::_prepareLayout();
    }

}
