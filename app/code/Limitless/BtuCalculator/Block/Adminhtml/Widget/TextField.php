<?php

namespace Limitless\BtuCalculator\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory;

Class TextField extends Template

{

    /**
     * @var Factory
     */
    protected $factoryElement;

    /**
     * TextField constructor.
     * @param Context $context
     * @param Factory $factoryElement
     * @param array $data
     */
    public function __construct(
        Context $context,
        Factory $factoryElement,
        $data = []
    ) {
        $this->factoryElement = $factoryElement;
        parent::__construct($context, $data);
    }

    public function prepareElementHtml(AbstractElement $element)
    {
        $textArea = $this->factoryElement->create('textarea', ['data' => $element->getData()])
            ->setId($element->getId())
            ->setForm($element->getForm())
            ->setClass('widget-option input-textarea admin__control-text');

        if ($element->getRequired()) {
            $textArea->addClass('required-entry');
        }

        $element->setData(
            'after_element_html',
            $this->_getAfterElementHtml() . $textArea->getElementHtml()
        );

        return $element;
    }


    protected function _getAfterElementHtml()
    {
        $html = <<<HTML
    <style>
        .admin__field-control.control .control-value {
            display: none !important;
        }
    </style>
HTML;

        return $html;
    }

}