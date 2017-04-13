<?php


namespace Limitless\RobotExclusions\Test\Integration;

use Magento\TestFramework\ObjectManager;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template\Context;
use Limitless\RobotExclusions\Block\RobotExclusions;
use Magento\Framework\App\Request\Http AS Request;

class RobotExclusionPageTest extends \PHPUnit_Framework_TestCase
{
    protected $contextMock;
    protected $requestMock;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function testRobotExclusionWillBeShown()
    {
        $this->requestMock->method('getParams')->willReturnCallback([$this, 'mockGetURLParamsThatWillShowRobotsExclusion']);

        /** @var RobotExclusions $robotExclusions */
        $robotExclusions = $this->objectManager->create(RobotExclusions::class, ['context'=>$this->contextMock, 'request'=>$this->requestMock]);
        $this->assertTrue($robotExclusions->shouldPageDisplayRobots());
    }

    public function testRobotExclusionWillBeNotShown()
    {
        $this->requestMock->method('getParams')->willReturnCallback([$this, 'mockGetURLParamsThatWillNotShowRobotsExclusion']);

        /** @var RobotExclusions $robotExclusions */
        $robotExclusions = $this->objectManager->create(RobotExclusions::class, ['context'=>$this->contextMock, 'request'=>$this->requestMock]);
        $this->assertFalse($robotExclusions->shouldPageDisplayRobots());
    }

    public function mockGetURLParamsThatWillNotShowRobotsExclusion()
    {
        return ['foo'=>'500mm', 'bar'=>'blue'];
    }

    public function mockGetURLParamsThatWillShowRobotsExclusion()
    {
        return ['width'=>'500mm', 'height'=>['500mm', '600mm']];
    }

    public function mockGetExclusionListFromScopeConfig($path, $scope)
    {
        $values = array(
            'default' => "width, height",
            'websites' => 'height, dude' ,
            'store' => 'colour, height'
        );

        return $values[$scope];
    }

    public function createEnvironment()
    {
        $this->requestMock = $this->createMock(Request::class);
        $scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $scopeConfigMock->method('getValue')->willReturnCallback([$this, 'mockGetExclusionListFromScopeConfig']);
        $this->contextMock = $this->objectManager->create(Context::class, ['scopeConfig' => $scopeConfigMock]);
    }

    protected function setUp()
    {
        $this->objectManager = ObjectManager::getInstance();
        $this->createEnvironment();
    }

}