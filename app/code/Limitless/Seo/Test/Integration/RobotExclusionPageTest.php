<?php

namespace Limitless\RobotExclusions\Test\Integration;

use Magento\TestFramework\ObjectManager;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template\Context;
use Limitless\Seo\Model\RobotsExclusions;
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
        $this->requestMock->method('getParams')->willReturnCallback([
            $this,
            'mockGetURLParamsThatWillShowRobotsExclusion'
        ]);
        /** @var \Limitless\Seo\Model\RobotsExclusions $robotExclusions */
        $robotExclusions = $this->objectManager->create(RobotsExclusions::class,
            ['context' => $this->contextMock]);
        $this->assertTrue($robotExclusions->shouldAssetBeNoFollow($this->requestMock->getParams()));
    }

    public function testRobotExclusionWillBeNotShown()
    {
        $this->requestMock->method('getParams')->willReturnCallback([
            $this,
            'mockGetURLParamsThatWillNotShowRobotsExclusion'
        ]);
        /** @var \Limitless\Seo\Model\RobotsExclusions $robotExclusions */
        $robotExclusions = $this->objectManager->create(RobotsExclusions::class,
            ['context' => $this->contextMock, 'request' => $this->requestMock]);
        $this->assertFalse($robotExclusions->shouldAssetBeNoFollow($this->requestMock->getParams()));
    }

    public function mockGetURLParamsThatWillNotShowRobotsExclusion()
    {
        return ['foo' => '500mm', 'bar' => 'blue'];
    }

    public function mockGetURLParamsThatWillShowRobotsExclusion()
    {
        return ['width' => '500mm', 'height' => ['500mm', '600mm']];
    }

    public function mockGetExclusionListFromScopeConfig($path, $scope)
    {
        $values = array(
            'default' => "width, height",
            'websites' => 'height, dude',
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