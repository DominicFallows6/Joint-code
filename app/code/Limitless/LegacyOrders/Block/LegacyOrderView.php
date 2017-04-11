<?php


namespace Limitless\LegacyOrders\Block;

use Limitless\LegacyOrders\Repository\LegacyOrderRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

class LegacyOrderView extends Template
{
    /**
     * @var bool
     */
    private $order;
    /**
     * @var Http
     */
    private $request;
    /**
     * @var LegacyOrderRepositoryInterface
     */
    private $legacyOrderRepository;
    /**
     * @var Session
     */
    private $customerSession;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    const ORDERIDLENGTH = 8;
    const ORDERPADDING = '0';

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        LegacyOrderRepositoryInterface $legacyOrderRepository,
        Session $customerSession,
        Http $request,
        $data = []
    ) {

        parent::__construct($context, $data);

        $this->order = false;
        $this->request = $request;
        $this->legacyOrderRepository = $legacyOrderRepository;
        $this->customerSession = $customerSession;
        $this->storeManager = $context->getStoreManager();
    }

    protected function _prepareLayout()
    {

        $this->pageConfig->getTitle()->set(__('Legacy Order').' #'.str_pad($this->request->getParam('order'), self::ORDERIDLENGTH, self::ORDERPADDING, STR_PAD_LEFT));

    }

    public function initLegacyOrderData()
    {
        $customerData = $this->customerSession->getCustomer()->getData();

        if (is_numeric($this->request->getParam('order'))) {
            $this->order = $this->legacyOrderRepository->getOrder($this->request->getParam('order'), $customerData['email'], $this->storeManager->getWebsite()->getId());
        } else {
            $this->order = false;
        }
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


    /**
     * @return bool | \Limitless\LegacyOrders\Model\LegacyOrders
     */
    public function getLegacyOrderData()
    {
        return $this->order;
    }

}