<?php

namespace Limitless\Pagination\Block;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Theme\Block\Html\Pager;
use Magento\Backend\Model\Menu\Item\Factory;

class View extends Pager
{

///private/var/www/html/magento2/vendor/magento/zendframework1/library/Zend/Navigation/Page/Mvc.php

    public function __construct(
        Template\Context $context,
        array $data = [],
        Collection $productCollection,
        Registry $registry,
        Factory $items
    )
    {
        parent::__construct($context, $data);
//        $this->categoryFactory = $categoryFactory;
//        $this->productCollection = $productCollection;
//        $this->products = $productCollection->load();
//        $this->setCollection($this->products);
//        $this->getProductCollection();


        /** @var \Magento\Catalog\Model\Category $category */
        $category = $registry->registry('current_category');


        $collection = $category->getProductCollection();
        $data = $this->setCollection($collection);

        $items->create($data);


    }
//
//    public function PaginationTest()
//    {
//        $actionName = $this->getAction()->getFullActionName();
//        if ($actionName == 'catalog_category_view') // Category Page
//        {
//            $category = Mage::registry('current_category');
//            $prodCol = $category->getProductCollection()->addAttributeToFilter('status', 1)->addAttributeToFilter('visibility', array('in' => array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG, Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)));
//            $tool = $this->getLayout()->createBlock('page/html_pager')->setLimit($this->getLayout()->createBlock('catalog/product_list_toolbar')->getLimit())->setCollection($prodCol);
//            $linkPrev = false;
//            $linkNext = false;
//            if ($tool->getCollection()->getSelectCountSql()) {
//                if ($tool->getLastPageNum() > 1) {
//                    if (!$tool->isFirstPage()) {
//                        $linkPrev = true;
//                        if ($tool->getCurrentPage() == 2) {
//                            $url = explode('?', $tool->getPreviousPageUrl());
//                            $prevUrl = @$url[0];
//                        } else {
//                            $prevUrl = $tool->getPreviousPageUrl();
//                        }
//                    }
//                    if (!$tool->isLastPage()) {
//                        $linkNext = true;
//                        $nextUrl = $tool->getNextPageUrl();
//                    }
//                }
//            }
//            if ($linkPrev) return '<link rel="prev" href="' . $prevUrl . '" />';
//            if ($linkNext) return '<link rel="next" href="' . $nextUrl . '" />';
//        }
//    }
}

