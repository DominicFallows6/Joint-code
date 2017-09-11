<?php


namespace Limitless\RobotExclusions\Block;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Request\Http as Request;
use Limitless\RobotExclusions\Model\RobotsExclusions as RobotsExclusionsModel;

class RobotExclusions extends Template
{

    /**
     * @var Request $request
     */
    private $request;

    /**
     * @var RobotsExclusionsModel
     */
    private $robotsExclusionsModel;

    public function __construct(
        Context $context,
        Request $request,
        RobotsExclusionsModel $robotsExclusionsModel,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->robotsExclusionsModel = $robotsExclusionsModel;
        $this->request = $request;

        if ($this->robotsExclusionsModel->shouldAssetBeNoFollow($this->request->getParams())){
            $this->pageConfig->setRobots('noindex, nofollow');
        }
    }

}