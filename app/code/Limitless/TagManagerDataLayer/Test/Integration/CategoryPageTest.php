<?php

namespace Limitless\TagManagerDataLayer\Test\Integration;

use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\AbstractController;

class CategoryPageTest extends AbstractController
{
    //TODO some test of ordering in catalog with multiple prods
    //TODO inc / ex vat
    private $expectedEmptyCategoryBlockOutput = <<<EOT
dataLayer.push({limitless_dl :{ 'google_tag_params':{'pagetype':'category','prodcategory':'Category 1','prodid':'','prodvalue':0,'totalvalue':0}}})
EOT;

    private $expectedOneProductCategoryBlockOutput = <<<EOT
dataLayer.push({limitless_dl :{ 'google_tag_params':{'pagetype':'category','prodcategory':'Category 1','prodid':["333"],'prodvalue':[10.00],'totalvalue':0}}})
EOT;

    private $expectedOneProductWithSkuAndPrefixCategoryBlockOutput = <<<EOT
dataLayer.push({limitless_dl :{ 'google_tag_params':{'pagetype':'category','prodcategory':'Category 1','prodid':["prefix_simple333"],'prodvalue':[10.00],'totalvalue':0}}})
EOT;

    /**
     * @magentoConfigFixture current_store google/analytics/active 1
     * @magentoConfigFixture current_store google/analytics/type tag_manager
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/general_settings/enabled 1
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/general_settings/datalayer_name limitless_dl
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/enabled 1
     *
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/product_id_value id
     * @magentoConfigFixture current_store limitless_tracking/dynamic_remarketing/general_settings/total_vat_setting include
     *
     * @magentoDataFixture Magento/Catalog/_files/category.php
     *
     * @magentoDbIsolation enabled
     */
    public function testEmptyCategoryTagContent()
    {
        $categoryId = 333;
        $objectManager = ObjectManager::getInstance();
        $this->dispatchCategoryPage($categoryId);

        //Asserts
        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());

        /** @var LayoutInterface $layout */
        $layout = $objectManager->get(LayoutInterface::class);
        $block = $layout->getBlock('limitless_gtm_datalayer_page_load');

        $this->assertNotFalse($block);
        $this->assertContains($this->expectedEmptyCategoryBlockOutput, $block->toHtml());
    }

    /**
     * @magentoConfigFixture current_store google/analytics/active 1
     * @magentoConfigFixture current_store google/analytics/type tag_manager
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/general_settings/enabled 1
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/general_settings/datalayer_name limitless_dl
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/enabled 1
     *
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/product_id_value id
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/total_vat_setting include
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/max_products_displayed 2
     *
     * @magentoDataFixture Magento/Catalog/_files/category_product.php
     *
     * @magentoDbIsolation enabled
     */
    public function testOneProductCategoryTagContent()
    {
        $categoryId = 333;
        $objectManager = ObjectManager::getInstance();
        $this->dispatchCategoryPage($categoryId);

        //Asserts
        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());

        /** @var LayoutInterface $layout */
        $layout = $objectManager->get(LayoutInterface::class);

        $block = $layout->getBlock('limitless_gtm_datalayer_page_load');
        $this->assertNotFalse($block);
        $this->assertContains($this->expectedOneProductCategoryBlockOutput, $block->toHtml());
    }

    /**
     * @magentoConfigFixture current_store google/analytics/active 1
     * @magentoConfigFixture current_store google/analytics/type tag_manager
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/general_settings/enabled 1
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/general_settings/datalayer_name limitless_dl
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/enabled 1
     *
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/product_id_value sku
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/product_id_prefix prefix_
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/total_vat_setting include
     * @magentoConfigFixture current_store google/limitless_tagmanager_datalayer/dynamic_remarketing/max_products_displayed 2
     *
     * @magentoDataFixture Magento/Catalog/_files/category_product.php
     *
     * @magentoDbIsolation enabled
     */
    public function testOneProductWithSkuAndPrefixCategoryTagContent()
    {
        $categoryId = 333;
        $objectManager = ObjectManager::getInstance();
        $this->dispatchCategoryPage($categoryId);

        //Asserts
        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());

        /** @var LayoutInterface $layout */
        $layout = $objectManager->get(LayoutInterface::class);
        $block = $layout->getBlock('limitless_gtm_datalayer_page_load');

        $this->assertNotFalse($block);
        $this->assertContains($this->expectedOneProductWithSkuAndPrefixCategoryBlockOutput, $block->toHtml());
    }

    private function dispatchCategoryPage($categoryId)
    {
        $objectManager = ObjectManager::getInstance();

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $objectManager->create(CategoryRepository::class);
        $catTestUrlKey = $categoryRepository->get($categoryId)->getCategoryIdUrl();

        $splitUrl = explode('/index.php/', $catTestUrlKey);
        if (isset($splitUrl[1])) {
            $this->dispatch($splitUrl[1]);
        } else {
            $this->fail('Could not gernerate dispatch URL');
        }
    }
}