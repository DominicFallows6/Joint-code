<?php

namespace Limitless\TagManagerDataLayer\Test\Integration;

use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\AbstractController;

class SearchPageTest extends AbstractController
{
    private $expectedBlockOutput = <<<EOT
dataLayer.push({limitless_dl :{ 'google_tag_params':{'pagetype':'searchresults','prodcategory':'','prodid':["simple", "simple_with_cross"],'prodvalue':[10,10],'totalvalue':0}}})
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
     * @magentoDataFixture Magento/Catalog/_files/products_in_category_rollback.php
     * @magentoDataFixture Magento/Catalog/_files/products_in_category.php
     *
     * @magentoDbIsolation enabled
     */
    public function noworking_testSearchPageTagContent()
    {
        $query = 'Simple';
        $objectManager = ObjectManager::getInstance();
        $this->dispatchSearchPage($query);

        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());

        /** @var LayoutInterface $layout */
        $layout = $objectManager->get(LayoutInterface::class);
        $block = $layout->getBlock('limitless_gtm_datalayer_page_load');

        $this->assertNotFalse($block);
        $this->assertContains($this->expectedBlockOutput, $block->toHtml());
    }

    private function dispatchSearchPage($query = '')
    {
        $this->getRequest()->setParam('q', $query);
        $this->dispatch('catalogsearch/result/');
    }
}