<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Controller\Adminhtml\Step;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page as BackendPage;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Limitless_SortCategoryProducts::batch_process';

    /**
     * @var PageFactory
     */
    private $pageFactory;

    public function __construct(Action\Context $context, PageFactory $pageFactory)
    {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
    }

    public function execute()
    {
        /** @var BackendPage $page */
        $page = $this->pageFactory->create();
        $page->setActiveMenu('Limitless_SortCategoryProducts::batch_process');
        $page->getConfig()->getTitle()->prepend('Batch Update Category Product Positions');
        return $page;
    }
}
