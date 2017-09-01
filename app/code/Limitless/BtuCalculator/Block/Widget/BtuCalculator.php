<?php

namespace Limitless\BtuCalculator\Block\Widget;

use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;

class BtuCalculator extends Template implements BlockInterface
{

    /**
     * @var Repository
     */
    protected $productAttributeRepository;

    /**
     * @var Attribute
     */
    protected $attributeFactory;

    public function __construct(
        Attribute $attributeFactory,
        Repository $productAttributeRepository,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->attributeFactory = $attributeFactory;
        $this->productAttributeRepository = $productAttributeRepository;
    }

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('Limitless_BtuCalculator::btu-calculator.phtml');
    }

    public function getFilterOutputAttributeLabel() {

        $filter_output = $this->productAttributeRepository->get('filter_output')->getOptions();
        $filter_output_label = [];

        foreach ($filter_output as $output) {
            $filter_output_label[] .= str_replace(' ', '',$output->getLabel());
        }

        return json_encode($filter_output_label);

    }

    public function getFilterOutputAttributeValue() {

        $filter_output = $this->productAttributeRepository->get('filter_output')->getOptions();
        $filter_output_value = [];

        foreach ($filter_output as $output) {
            $filter_output_value[] .= str_replace(' ', '',$output->getValue());
        }

        return json_encode($filter_output_value);

    }

}