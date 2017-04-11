<?php


namespace Limitless\LegacyOrders\Block;

use Limitless\LegacyOrders\Repository\LegacyOrderRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

class CheckForLegacy extends Template
{
    /**
     * @var bool
     */
    private $showLegacyButton = false;
    /**
     * @var Session
     */
    private $customerSession;
    /**
     * @var LegacyOrderRepositoryInterface
     */
    private $legacyOrderRepository;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Session $customerSession,
        LegacyOrderRepositoryInterface $legacyOrderRepository,
        $data = []
    ) {

        parent::__construct($context, $data);

        $this->customerSession = $customerSession;
        $this->legacyOrderRepository = $legacyOrderRepository;
        $this->storeManager = $context->getStoreManager();
    }

    public function getShowLegacyButton()
    {
        return $this->showLegacyButton;
    }


    public function checkForLegacyOrderListFromCustomerData()
    {
        $customerData = $this->customerSession->getCustomer()->getData();

        if ($this->legacyOrderRepository->listOrders($customerData['email'], $this->storeManager->getWebsite()->getId())) {
            $this->showLegacyButton = true;
        }
    }


}