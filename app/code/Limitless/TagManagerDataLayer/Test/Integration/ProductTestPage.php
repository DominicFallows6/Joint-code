<?php

namespace Limitless\TagManagerDataLayer\Test\Integration;

use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\AbstractController;

class ProductPageTest extends AbstractController
{
    //TODO test product in multiple categories
    //Todo test inc / ex vat
    private $expectedProductExpectedOutput = <<<EOT
dataLayer.push({limitless_dl :{ 'google_tag_params':{'pagetype':'product','prodcategory':'Category 1','prodid':["333"],'prodvalue':[10.00],'totalvalue':10.00}}})
EOT;

    private $expectedProductMultipleCatsExpectedOutput = <<<EOT
dataLayer.push({limitless_dl :{ 'google_tag_params':{'pagetype':'product','prodcategory':'Category First','prodid':["333"],'prodvalue':[10.00],'totalvalue':10.00}}})
EOT;


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
    public function testProductTagContent()
    {
        $productId = 333;
        $categoryId = 333;
        $objectManager = ObjectManager::getInstance();
        $this->dispatchProductPageInCategory($categoryId, $productId);

        //Asserts
        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());

        /** @var LayoutInterface $layout */
        $layout = $objectManager->get(LayoutInterface::class);
        $block = $layout->getBlock('limitless_gtm_datalayer_page_load');

        $this->assertNotFalse($block);
        $this->assertContains($this->expectedProductExpectedOutput, $block->toHtml());
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
     * @magentoDataFixture ../../../../app/code/Limitless/TagManagerDataLayer/Test/Integration/Catalog/_files/product_in_multiple_categories.php
     *
     * @magentoDbIsolation enabled
     */
    public function testProductInMultipleCategoriesTagContent()
    {
        $productId = 333;
        $categoryId = 401;

        $objectManager = ObjectManager::getInstance();
        $this->dispatchProductPageInCategory($categoryId, $productId);

        //Asserts
        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());

        /** @var LayoutInterface $layout */
        $layout = $objectManager->get(LayoutInterface::class);
        $block = $layout->getBlock('limitless_gtm_datalayer_page_load');

        $this->assertNotFalse($block);
        $this->assertContains($this->expectedProductMultipleCatsExpectedOutput, $block->toHtml());
    }

    private function dispatchProductPageInCategory($categoryId, $productId)
    {
        $objectManager = ObjectManager::getInstance();

        /** @var Product $product */
        $product = $objectManager->create(Product::class);

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $objectManager->create(CategoryRepository::class);
        $catPath = $categoryRepository->get($categoryId)->getPath();

        $product->setRequestPath($catPath);
        $this->dispatch('catalog/product/view/id/'.$productId);
    }
}