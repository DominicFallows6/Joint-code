<?php


namespace Limitless\RobotExclusions\Block;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Request\Http AS Request;

class RobotExclusions extends Template
{

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Http
     */
    private $request;
    private $urlParams = [];
    private $urlParamsToExcludeRawString;
    private $urlParamsToExclude = [];

    public function __construct(
        Context $context,
        Request $request,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->scopeConfig = $context->getScopeConfig();
        $this->request = $request;

        $this->urlParams = $this->request->getParams();
        $this->processExclusions();

        if ($this->shouldPageDisplayRobots()){
            $this->pageConfig->setRobots('noindex, nofollow');
        }
    }

    public function shouldPageDisplayRobots():bool
    {
        $result = false;

        if (!empty($this->urlParamsToExclude)) {
            foreach ($this->urlParams as $exclusionKey => $exclusionValue){
                if (in_array($exclusionKey, $this->urlParamsToExclude)) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    private function processExclusions()
    {
        $this->getRobotExclusions();
        $this->convertExclusionsFromListToArray();
    }

    private function convertExclusionsFromListToArray()
    {
        $result = explode(',', $this->urlParamsToExcludeRawString);
        $this->urlParamsToExclude = array_map('trim', $result);
    }

    private function getRobotExclusions()
    {
        $this->urlParamsToExcludeRawString = $this->scopeConfig->getValue('web/limitless_robot_exclusions/robot_exclusions', ScopeInterface::SCOPE_STORE);
    }

}