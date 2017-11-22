<?php

namespace Limitless\Pagination\Block;

use Magento\Catalog\Block\Category\View as ViewParent;
use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Helper\Category;
use Magento\Framework\View\Page\Config;
use Magento\Theme\Block\Html\Pager;

class Pagination extends ViewParent
{


    /** @var $registry Registry */
    public $registry = null;
    /** @var  $catalog \Magento\Catalog\Model\Category */
    public $category = null;
    /** @var $context \Magento\Framework\View\Element\Template\Context */
    public $context = null;
    /** @var $toolbar Toolbar; */
    public $toolbar = null;
    /** @var $pager Pager */
    public $pager = null;
    /** @var Resolver */
    private $layerResolver;

    /**
     * Pagination constructor.
     * @param Template\Context $context
     * @param array $data
     * @param Registry $registry
     * @param Toolbar $toolbar
     * @param Pager $pager
     * @param Config $pageConfig
     * @param Resolver $layerResolver
     */
    public function __construct(
        Template\Context $context,
        array $data = [],
        Registry $registry,
        Toolbar $toolbar,
        Pager $pager,
        Config $pageConfig,
        Resolver $layerResolver,
        Category $categoryHelper
    )
    {
        parent::__construct(
            $context,
            $layerResolver,
            $registry,
            $categoryHelper,
            $data
        );
        $this->context = $context;
        $this->registry = $registry;
        $this->category = $registry->registry('current_category');
        $this->toolbar = $toolbar;
        $this->pager = $pager;
        $this->pageConfig = $pageConfig;

    }


    protected function _prepareLayout()
    {
        $this->toolbar->setCollection($this->category->getProductCollection());
        $tool = $this->pager->setCollection($this->category->getProductCollection());
        $tool->setShowPerPage($this->toolbar->getLimit());
        $has_prev_page = $has_next_page = false;
        $next_page_url = $prev_page_url = '';
        $last_page = intval((($this->category->getProductCollection()->count() - 1) / $this->toolbar->getLimit()) + 1);
        $current_page = $this->toolbar->getCurrentPage();
        if ($current_page != 1 && $this->toolbar->getLastPageNum() != 1) {
            $has_prev_page = true;
            $params = $this->getRequest()->getParams();
            $params['id'] = null;
            $params[$tool->getPageVarName()] = $current_page - 1;
            $prev_page_url = $this->toolbar->getPagerUrl($params);
        }

        if ($current_page != $last_page) {
            $has_next_page = true;
            $params = $this->getRequest()->getParams();
            $params['id'] = null;
            $params[$tool->getPageVarName()] = $current_page + 1;
            $next_page_url = $this->toolbar->getPagerUrl($params);
        }
        if ($has_prev_page) {
            $this->pageConfig->addRemotePageAsset(
                $prev_page_url,
                'link_rel',
                ['attributes' => ['rel' => 'prev']]
            );
        }
        if ($has_next_page) {
            $this->pageConfig->addRemotePageAsset(
                $next_page_url,
                'link_rel',
                ['attributes' => ['rel' => 'next']]
            );
        }
    }
}