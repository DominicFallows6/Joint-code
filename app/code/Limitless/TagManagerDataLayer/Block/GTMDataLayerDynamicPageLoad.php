<?php

namespace Limitless\TagManagerDataLayer\Block;

use Limitless\TagManagerDataLayer\Helper\DataLayerBlockLocator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Request\Http;

class GTMDataLayerDynamicPageLoad extends Template
{
    /** @var Template */
    private $contentBlock;

    public function __construct(
        Context $context,
        Http $httpRequest,
        DataLayerBlockLocator $dataLaterBlockLocator,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $currentPageActionName = $httpRequest->getFullActionName();

        if ($dataLaterBlockLocator->isValid($currentPageActionName)) {
            $this->contentBlock = $dataLaterBlockLocator->locate($currentPageActionName);
        }
    }

    /*
    * Decides whether:
    * 1) Display template (set in DataLayerAbstract->setTemplate)
    * 2) Return empty string
    */
    protected function _toHtml()
    {
        if ($this->contentBlock) {
            return $this->contentBlock->toHtml();
        }
        return '';
    }
}