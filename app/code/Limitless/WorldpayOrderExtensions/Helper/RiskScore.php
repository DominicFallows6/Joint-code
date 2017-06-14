<?php

namespace Limitless\WorldpayOrderExtensions\Helper;

use Braintree\Exception;
use Limitless\WorldpayOrderExtensions\Model\WorldpayRiskScore;
use Limitless\WorldpayOrderExtensions\Model\WorldpayRiskScoreFactory;

class RiskScore
{
    /** @var WorldpayRiskScoreFactory */
    private $worldpayRiskScoreFactory;

    public function __construct(
        WorldpayRiskScoreFactory $worldpayRiskScoreFactory
    ) {
        $this->worldpayRiskScoreFactory = $worldpayRiskScoreFactory;
    }

    /**
     * @param integer $orderId
     * @param integer $riskScore
     * @return bool
     */
    public function saveRiskScore($orderId, $riskScore = null)
    {
        /** @var WorldpayRiskScore $worldpayRiskScoreModel */
        $worldpayRiskScoreModel = $this->worldpayRiskScoreFactory->create();

        $worldpayRiskScoreModel->getResource()->load($worldpayRiskScoreModel, $orderId, WorldpayRiskScore::ORDER_ID);

        if ($worldpayRiskScoreModel->getId()){
            $worldpayRiskScoreModel->setData(WorldpayRiskScore::RISK_SCORE, $riskScore);
        } else {
            $worldpayRiskScoreModel->setData(WorldpayRiskScore::ORDER_ID, $orderId);
            $worldpayRiskScoreModel->setData(WorldpayRiskScore::RISK_SCORE, $riskScore);
        }

        try {
            $worldpayRiskScoreModel->getResource()->save($worldpayRiskScoreModel);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}