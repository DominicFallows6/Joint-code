<?php

namespace Limitless\TagManagerDataLayer\Test\Integration;

use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\AbstractController;

class CartPageTest extends AbstractController
{
    private $expectedCartSingleBlockOutput = <<<EOT
dataLayer.push({limitless_dl :{ 'google_tag_params':{'pagetype':'cart','prodcategory':'','prodid':["simple"],'prodvalue':[10.00],'totalvalue':10.00}}})
EOT;

    private $expectedCartMultipleLineTotalBlockOutput = <<<EOT
dataLayer.push({limitless_dl :{ 'google_tag_params':{'pagetype':'cart','prodcategory':'','prodid':["simple"],'prodvalue':[30.00],'totalvalue':30.00}}})
EOT;

    private $expectedCartMultipleIndividualBlockOutput = <<<EOT
dataLayer.push({limitless_dl :{ 'google_tag_params':{'pagetype':'cart','prodcategory':'','prodid':["simple"],'prodvalue':[10.00],'totalvalue':30.00}}})
EOT;

    private $expectedCartMultipleDifferentOverMaxBlockOutput = <<<EOT
dataLayer.push({limitless_dl :{ 'google_tag_params':{'pagetype':'cart','prodcategory':'','prodid':["simple1","simple2"],'prodvalue':[30.00,40.00],'totalvalue':80.00}}})
EOT;

    /**
     * @magentoConfigFixture current_store google/analytics/active 1
     * @magentoConfigFixture current_store google/analytics/type tag_manager
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/general_settings/enabled 1
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/general_settings/datalayer_name limitless_dl
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/enabled 1
     *
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/product_id_value sku
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/total_vat_setting include
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/max_products_displayed 2
     *
     * @magentoDataFixture Magento/Checkout/_files/quote_with_simple_product.php
     *
     * @magentoDbIsolation enabled
     */
    public function testSingleCartPageTagContent()
    {
        $objectManager = ObjectManager::getInstance();
        $this->dispatchCartPage();

        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());

        /** @var LayoutInterface $layout */
        $layout = $objectManager->get(LayoutInterface::class);
        $block = $layout->getBlock('limitless_gtm_datalayer_page_load');

        $this->assertNotFalse($block);
        $this->assertContains($this->expectedCartSingleBlockOutput, $block->toHtml());
    }

    /**
     * @magentoConfigFixture current_store google/analytics/active 1
     * @magentoConfigFixture current_store google/analytics/type tag_manager
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/general_settings/enabled 1
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/general_settings/datalayer_name limitless_dl
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/enabled 1
     *
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/product_id_value sku
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/total_vat_setting include
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/max_products_displayed 2
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/total_display_summary total
     *
     * @magentoDataFixture ../../../../app/code/Limitless/TagManagerDataLayer/Test/Integration/Cart/_files/quote_with_multiple_simple_products.php
     *
     * @magentoDbIsolation enabled
     */
    public function testMultipleCartPageTagContent()
    {
        $objectManager = ObjectManager::getInstance();
        $this->dispatchCartPage();

        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());

        /** @var LayoutInterface $layout */
        $layout = $objectManager->get(LayoutInterface::class);
        $block = $layout->getBlock('limitless_gtm_datalayer_page_load');

        $this->assertNotFalse($block);
        $this->assertContains($this->expectedCartMultipleLineTotalBlockOutput, $block->toHtml());
    }

    /**
     * @magentoConfigFixture current_store google/analytics/active 1
     * @magentoConfigFixture current_store google/analytics/type tag_manager
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/general_settings/enabled 1
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/general_settings/datalayer_name limitless_dl
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/enabled 1
     *
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/product_id_value sku
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/total_vat_setting include
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/max_products_displayed 2
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/total_display_summary individual
     *
     * @magentoDataFixture ../../../../app/code/Limitless/TagManagerDataLayer/Test/Integration/Cart/_files/quote_with_multiple_simple_products.php
     *
     * @magentoDbIsolation enabled
     */
    public function testMultipleWithIndividualCartPageTagContent()
    {
        $objectManager = ObjectManager::getInstance();
        $this->dispatchCartPage();

        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());

        /** @var LayoutInterface $layout */
        $layout = $objectManager->get(LayoutInterface::class);
        $block = $layout->getBlock('limitless_gtm_datalayer_page_load');

        $this->assertNotFalse($block);
        $this->assertContains($this->expectedCartMultipleIndividualBlockOutput, $block->toHtml());
    }

    /**
     * @magentoConfigFixture current_store google/analytics/active 1
     * @magentoConfigFixture current_store google/analytics/type tag_manager
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/general_settings/enabled 1
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/general_settings/datalayer_name limitless_dl
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/enabled 1
     *
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/product_id_value sku
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/total_vat_setting include
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/max_products_displayed 2
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/total_display_summary total
     *
     * @magentoDataFixture ../../../../app/code/Limitless/TagManagerDataLayer/Test/Integration/Cart/_files/quote_with_multiple_different_simple_products.php
     *
     * @magentoDbIsolation enabled
     */
    public function testMultipleDifferentOverMaxCartPageTagContent()
    {
        $objectManager = ObjectManager::getInstance();
        $this->dispatchCartPage();

        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());

        /** @var LayoutInterface $layout */
        $layout = $objectManager->get(LayoutInterface::class);
        $block = $layout->getBlock('limitless_gtm_datalayer_page_load');

        $this->assertNotFalse($block);
        $this->assertContains($this->expectedCartMultipleDifferentOverMaxBlockOutput, $block->toHtml());
    }


    private function dispatchCartPage()
    {
        $this->dispatch('checkout/cart/index');
    }
}