<?php

namespace Limitless\LegacyOrders\Controller\Order;

use Magento\Framework\App\Action\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;

abstract class BaseAction extends Action
{
    /** 
     * @var PageFactory  
     */
    private $pageFactory;
    /**
     * @var Session
     */
    private $customerSession;
    /**
     * @var Context
     */
    private $context;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Session $customerSession
    )
    {

        parent::__construct($context);
        $this->pageFactory = $pageFactory;

        $this->customerSession = $customerSession;
        $this->context = $context;
    }

    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {
            $this->customerSession->setAfterAuthUrl($this->context->getUrl()->getCurrentUrl());
            $this->customerSession->authenticate();
            $this->getResponse()->sendResponse();
        }

        $resultPage = $this->pageFactory->create();
        return $resultPage;

    }
}