<?php

namespace Limitless\TagManagerDataLayer\Test\Integration;

use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\AbstractController;

class HomePageTest extends AbstractController
{
    private $expectedBlockOutput = <<<EOT
dataLayer.push({limitless_dl :{ 'google_tag_params':{'pagetype':'home','prodcategory':'','prodid':'','prodvalue':0,'totalvalue':0}}})
EOT;

    /**
     * @magentoConfigFixture current_store google/analytics/active 1
     * @magentoConfigFixture current_store google/analytics/type tag_manager
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/general_settings/enabled 1
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/general_settings/datalayer_name limitless_dl
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/enabled 1
     *
     * @magentoDbIsolation enabled
     */
    public function testHomePageTagContent()
    {
        $objectManager = ObjectManager::getInstance();

        $this->dispatch('cms/index/index');

        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());

        /** @var LayoutInterface $layout */
        $layout = $objectManager->get(LayoutInterface::class);
        $block = $layout->getBlock('limitless_gtm_datalayer_page_load');

        $this->assertNotFalse($block);
        $this->assertContains($this->expectedBlockOutput, $block->toHtml());
    }
}