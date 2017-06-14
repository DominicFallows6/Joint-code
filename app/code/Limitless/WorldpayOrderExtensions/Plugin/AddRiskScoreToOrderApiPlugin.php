<?php

namespace Limitless\WorldpayOrderExtensions\Plugin;

use Limitless\WorldpayOrderExtensions\Model\WorldpayRiskScore;
use Limitless\WorldpayOrderExtensions\Model\WorldpayRiskScoreFactory;
use Magento\Sales\Api\Data\OrderPaymentExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

class AddRiskScoreToOrderApiPlugin
{

    /** @var OrderPaymentExtensionFactory */
    private $orderPaymentExtensionFactory;

    /** @var WorldpayRiskScoreFactory */
    private $worldpayRiskScoreFactory;

    public function __construct(
        OrderPaymentExtensionFactory $orderPaymentExtensionFactory,
        WorldpayRiskScoreFactory $worldpayRiskScoreFactory
    ) {
        $this->orderPaymentExtensionFactory = $orderPaymentExtensionFactory;
        $this->worldpayRiskScoreFactory = $worldpayRiskScoreFactory;
    }

    public function afterGet(OrderRepositoryInterface $subject, $order)
    {
        /** @var Order $order */
        $this->addExtensionAttributesToOrderPayment($order);
        return $order;
    }

    /**
     * @param Order $order
     */
    private function addExtensionAttributesToOrderPayment($order)
    {
        $orderId = $order->getRealOrderId();
        $payment = $order->getPayment();
        $extensionAttributes = $payment->getExtensionAttributes();

        if (!$extensionAttributes) {
            $extensionAttributes = $this->orderPaymentExtensionFactory->create();
            $payment->setExtensionAttributes($extensionAttributes);
        }

        $worldpayRiskScore = $this->worldpayRiskScoreFactory->create();
        $worldpayRiskScore->getResource()->load($worldpayRiskScore, $orderId, WorldpayRiskScore::ORDER_ID);

        $riskScore = null;
        if ($worldpayRiskScore->getId()) {
            $riskScore = $worldpayRiskScore->getData(WorldpayRiskScore::RISK_SCORE);
        }

        if (null !== $riskScore) {
            $extensionAttributes->setRiskScore($riskScore);
        }
    }
}
