<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Block\Adminhtml\InitialStep;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form as FormWidget;
use Magento\Framework\Data\FormFactory;

class Form extends FormWidget
{
    const FILE_PARAM = 'batch_file';

    /**
     * @var FormFactory
     */
    private $formFactory;

    public function __construct(Context $context, FormFactory $formFactory, array $data = [])
    {
        parent::__construct($context, $data);
        $this->formFactory = $formFactory;
    }

    protected function _prepareForm()
    {
        $form = $this->formFactory->create([
            'data' => [
                'id'            => 'edit_form',
                'action'        => $this->getUrl('*/*/validate'),
                'method'        => 'post',
                'enctype'       => 'multipart/form-data',
                'use_container' => true,
            ],
        ]);
        $fieldset = $form->addFieldset('batch_sort', [
            ['legend' => __('Category Product Positions Import File'), 'collapsible' => false],
        ]);

        $fieldset->addField('batch_file', 'file', [
            'name'     => self::FILE_PARAM,
            'required' => true,
            'label'    => __('CSV Batch File'),
            'title'    => __('CSV Batch File'),
            'note'     => __('CSV File Columns: CategoryID, SKU, Position'),
        ]);

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
