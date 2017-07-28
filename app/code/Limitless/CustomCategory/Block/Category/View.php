<?php

namespace Limitless\CustomCategory\Block\Category;

use Magento\Catalog\Block\Category\View as ViewParent;
use Magento\Catalog\Helper\Category;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Request\Http;
use Magento\Store\Model\StoreManagerInterface;
use Limitless\CustomCategory\Repository\CustomCategoryRepositoryInterface;

class View extends ViewParent
{
    /**
     * @var Http
     */
    private $request;
    /**
     * @var Resolver
     */
    private $layerResolver;
    /**
     * @var CustomCategoryRepositoryInterface
     */
    private $customCategoryRepository;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Context $context,
        Resolver $layerResolver,
        Registry $registry,
        Category $categoryHelper,
        Http $request,
        CustomCategoryRepositoryInterface $customCategoryRepository,
        array $data = []
    ){
        parent::__construct(
            $context,
            $layerResolver,
            $registry,
            $categoryHelper,
            $data
        );

        $this->request = $request;
        $this->customCategoryRepository = $customCategoryRepository;
        $this->storeManager = $context->getStoreManager();
    }

    /**
     * @return string
     */
    public function getCustomCategoryDescription()
    {
        if ($this->hasCustomCategory() == true) {
            return $this->getCustomDescription();
        } else {
            return '';
        }
    }
    /**
     * @return int
     */
    public function getCurrentStoreId() {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @param int $currentCategoryId
     * @param array|int $currentAttributeId
     * @return \Limitless\CustomCategory\Model\CustomCategory
     */
    public function getCustomCategoryFromDb($currentCategoryId, $currentAttributeId)
    {
        $category = $this->customCategoryRepository->getCustomCategory($currentCategoryId, '1', $this->getCurrentStoreId(), $currentAttributeId);
        return $category;
    }

    /**
     * @return string
     */
    public function getFilterAttributeIdsAsString()
    {
        // Current filter attribute params minus category ID
        $currentAttributeId = $this->request->getParams();

        unset($currentAttributeId['id']);
        $currentAttributeId = $this->flatten($currentAttributeId);
        sort($currentAttributeId);

        return implode('|', $currentAttributeId);
    }

    /**
     * @param mixed[] $array
     * @return array
     */
    public function flatten($array)
    {
        $lst = array();
        foreach( array_keys($array) as $k ) {
            $v = $array[$k];
            if (is_scalar($v)) {
                $lst[] = $v;
            } else if (is_array($v)) {
                $lst = array_merge( $lst,
                    $this->flatten($v)
                );
            }
        };
        return $lst;
    }

    /**
     * @return \Limitless\CustomCategory\Model\CustomCategory
     */
    public function getCategory()
    {
        $currentCategoryId = $this->getCurrentCategory()->getId();
        if(!$currentCategoryId) {
            $currentCategoryId ='0';
        }
        $currentAttributeId = $this->getFilterAttributeIdsAsString();
        $category = $this->getCustomCategoryFromDb($currentCategoryId, $currentAttributeId);
        return $category;
    }

    /**
     * @return string|null
     */
    public function getCustomHeading()
    {
        $categoryFromDb = $this->getCategory();
        return $categoryFromDb->getData('category_heading');
    }

    /**
     * @return string|null
     */
    public function getCustomDescription()
    {
        $categoryFromDb = $this->getCategory();
        return $categoryFromDb->getData('category_description');
    }

    /**
     * @return bool
     */
    public function hasCustomCategory()
    {
        return (bool) $this->getCategory()->getId();
    }

    protected function _prepareLayout()
    {
        $this->getLayout()->createBlock('Magento\Catalog\Block\Breadcrumbs');

        $category = $this->getCurrentCategory();
        if ($category) {
            $title = $category->getMetaTitle();
            if ($title) {
                $this->pageConfig->getTitle()->set($title);
            }
            $description = $category->getMetaDescription();
            if ($description) {
                $this->pageConfig->setDescription($description);
            }
            $keywords = $category->getMetaKeywords();
            if ($keywords) {
                $this->pageConfig->setKeywords($keywords);
            }
            if ($this->_categoryHelper->canUseCanonicalTag()) {
                $this->pageConfig->addRemotePageAsset(
                    $category->getUrl(),
                    'canonical',
                    ['attributes' => ['rel' => 'canonical']]
                );
            }

            $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
            if ($this->hasCustomCategory() === true) {
                $categoryHeading = $this->getCustomHeading();
            } else {
                $categoryHeading = '';
            }
            if ($pageMainTitle && $categoryHeading) {
                $pageMainTitle->setPageTitle($categoryHeading);
            } else {
                $pageMainTitle->setPageTitle($this->getCurrentCategory()->getName());
            }
        }
    }
}