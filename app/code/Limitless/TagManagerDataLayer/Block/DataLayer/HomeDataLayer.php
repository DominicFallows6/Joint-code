<?php

namespace Limitless\TagManagerDataLayer\Block\DataLayer;

use Limitless\TagManagerDataLayer\Api\DataLayerAbstract;
use Limitless\TagManagerDataLayer\Helper\TagsDataLayer\DynamicRemarketing;
use Magento\Framework\View\Element\Template\Context;

class HomeDataLayer extends DataLayerAbstract
{
    /** @var array */
    private $dataLayerVariables;

    /** @var DynamicRemarketing */
    private $dynamicRemarketingHelper;

    public function __construct(
        Context $context,
        DynamicRemarketing $dynamicRemarketingHelper,
        $data = []
    ) {
        parent::__construct($context, $data);

        $this->dynamicRemarketingHelper = $dynamicRemarketingHelper;
        $this->dataLayerVariables = [];
    }

    public function initDataLayerVariables()
    {
        $this->initDynamicRemarketingDLVariables();
    }

    /**
     * @return array
     */
    public function getDataLayerVariables(): array
    {
        return $this->dataLayerVariables;
    }

    private function initDynamicRemarketingDLVariables()
    {
        $this->dynamicRemarketingHelper->buildAllDynamicRemarketingValues('home');

        $this->mergeIntoDataLayer($this->dynamicRemarketingHelper->getAllDynamicRemarketingValuesInArray());
    }

    /**
     * @param array $mergeRequest
     */
    private function mergeIntoDataLayer($mergeRequest)
    {
        $this->dataLayerVariables = array_merge($mergeRequest, $this->dataLayerVariables);
    }
}