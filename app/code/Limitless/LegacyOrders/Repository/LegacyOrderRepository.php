<?php


namespace Limitless\LegacyOrders\Repository;

use Limitless\LegacyOrders\Model\LegacyOrdersFactory;

class LegacyOrderRepository implements LegacyOrderRepositoryInterface
{

    /** @var $legacyOrdersFactory LegacyOrders  */
    private $legacyOrdersFactory;

    public function __construct(LegacyOrdersFactory $legacyOrdersFactory)
    {
        $this->legacyOrdersFactory = $legacyOrdersFactory;
    }


    /**
     * @param string $emailAddress
     * @param int $siteId
     * @param bool $legacyOrderId
     * @return \Limitless\LegacyOrders\Model\ResourceModel\LegacyOrders\Collection
     */
    public function listOrders($emailAddress, $siteId, $legacyOrderId = false)
    {

        $legacyOrders = $this->legacyOrdersFactory->create();
        $collection = $legacyOrders->getCollection();
        $collection->addFieldToFilter('user_email', $emailAddress)->addFieldToFilter('site_id', $siteId);

        if (is_numeric($legacyOrderId)) {
            $collection->addFieldToFilter('legacy_order_id', $legacyOrderId);
        }

        return $collection;

    }

    /**
     * @param bool $legacyOrderId
     * @param string $emailAddress
     * @param int $siteId
     * @return \Limitless\LegacyOrders\Model\LegacyOrders
     */

    public function getOrder($legacyOrderId, $emailAddress, $siteId)
    {

        $collection = $this->listOrders($emailAddress, $siteId, $legacyOrderId);
        return $collection->getFirstItem();

    }

}