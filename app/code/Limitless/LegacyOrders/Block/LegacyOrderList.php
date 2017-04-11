<?php


namespace Limitless\LegacyOrders\Block;

use Limitless\LegacyOrders\Repository\LegacyOrderRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

class LegacyOrderList extends Template
{
    /** 
     * @var LegacyOrderRepositoryInterface
     */
    private $legacyOrderRepository;
    /** 
     * @var  \Limitless\LegacyOrders\Model\ResourceModel\LegacyOrders\Collection $orderList 
     */
    private $orderList;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Session
     */
    private $customerSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        LegacyOrderRepositoryInterface $legacyOrderRepository,
        Session $customerSession,
        $data = []
    ) {
        parent::__construct($context, $data);

        $this->orderList = false;
        $this->legacyOrderRepository = $legacyOrderRepository;
        $this->storeManager = $context->getStoreManager();
        $this->customerSession = $customerSession;
    }

    protected function _prepareLayout()
    {
        $title = __('your legacy orders');
        $this->pageConfig->getTitle()->set($title);
    }

    public function initLegacyOrderList()
    {
        $customerData = $this->customerSession->getCustomer()->getData();
        
        $this->orderList = $this->legacyOrderRepository->listOrders($customerData['email'], $this->storeManager->getWebsite()->getId());
    }

    public function getOrderPhase($orderPhase)
    {
      if ($orderPhase == 'cancel') {
            $phase = 'cancelled';
        } else {
            $phase =  $orderPhase;
        }
        return $phase;
    }

    public function getOrderList()
    {
        return $this->orderList;
    }
    


}