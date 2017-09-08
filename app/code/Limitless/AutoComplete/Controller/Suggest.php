<?php

namespace Limitless\AutoComplete\Controller;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Search\Controller\Ajax\Suggest as Magento2Suggest;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Search\Model\AutocompleteInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\ScopeInterface;

class Suggest extends Magento2Suggest
{

    /**
     * @var AutocompleteInterface $autoComplete
     */
    private $autoComplete;

    /**
     * @var ScopeConfigInterface $scopeConfig
     */
    private $scopeConfig;

    public function __construct(
        Context $context,
        AutocompleteInterface $autoComplete,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->autoComplete = $autoComplete;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $autoComplete);
    }

    public function execute(): ResultInterface
    {
        if (!$this->getRequest()->getParam('q', false)) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_url->getBaseUrl());
            return $resultRedirect;
        }

        $autoCompleteData = $this->autoComplete->getItems();
        $responseData = [];
        foreach ($autoCompleteData as $resultItem) {
            $responseData[] = $resultItem->toArray();
        }

        //get number to return
        $sliceNumber = $this->scopeConfig->getValue('catalog/search/autocomplete_result_count',
            ScopeInterface::SCOPE_WEBSITE);
        $resultsToReturn = array_slice($responseData, 0, $sliceNumber);

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($resultsToReturn);
        return $resultJson;
    }
}