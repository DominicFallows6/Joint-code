<?php


namespace Limitless\LegacyOrders\Repository;

interface LegacyOrderRepositoryInterface
{

    /**
     * @param string $emailAddress
     * @param int $siteId
     * @param bool $legacyOrderId
     * @return mixed
     */
    public function listOrders($emailAddress, $siteId, $legacyOrderId = false);

    /**
     * @param bool $legacyOrderId
     * @param string $emailAddress
     * @param int $siteId
     * @return mixed
     */
    public function getOrder($legacyOrderId, $emailAddress, $siteId);

}

