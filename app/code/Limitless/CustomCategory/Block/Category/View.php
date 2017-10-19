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
    /** @var \Magento\Catalog\Model\Category $currentCategory */
    protected $currentCategory;

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
        // Current filter attribute params minus category ID and other sorting params
        $currentAttributeId = $this->removeUnwantedParams($this->request->getParams());

        $currentAttributeId = $this->flatten($currentAttributeId);
        sort($currentAttributeId);

        return implode('|', $currentAttributeId);
    }

    private function removeUnwantedParams(array $params) : array
    {
        unset($params['id']);

        if (isset($params['p'])) {
            unset($params['p']);
        }

        if (isset($params['product_list_order'])) {
            unset($params['product_list_order']);
        }

        if (isset($params['product_list_limit'])) {
            unset($params['product_list_limit']);
        }

        return $params;
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

    /**
     * @return mixed
     */
    public function getCustomMetaDescription()
    {
        $categoryFromDb = $this->getCategory();
        return $categoryFromDb->getData('meta_description');
    }

    /**
     * @return string
     */
    private function getCanonicalLinkForCustomCategory()
    {
        if ($this->hasCustomCategory() === true) {

            //get the base URL for the category
            $fullyQualifiedUrl = $this->currentCategory->getUrl();

            //remove the parameter string
            $strippedURL = str_replace('?'.$this->request->getUri()->getQuery(), '', $fullyQualifiedUrl);

            //add wanted params
            $paramsToAdd = $this->removeUnwantedParams($this->request->getParams());

            //build the full URL
            if (!empty($paramsToAdd)) {
                $fullyQualifiedUrl = $strippedURL.'?'.http_build_query($paramsToAdd);
            } else {
                $fullyQualifiedUrl = $strippedURL;
            }

            return $fullyQualifiedUrl;

        } else {
            return $this->currentCategory->getUrl();
        }
    }

    protected function _prepareLayout()
    {
        $this->getLayout()->createBlock('Magento\Catalog\Block\Breadcrumbs');

        $this->currentCategory = $this->getCurrentCategory();
        if ($this->currentCategory) {
            $customMetaTitle = $this->getCustomHeading();
            $title = $this->currentCategory->getMetaTitle();
            if ($customMetaTitle) {
                $this->pageConfig->getTitle()->set($customMetaTitle);
            } else if ($title) {
                $this->pageConfig->getTitle()->set($title);
            }

            $customMetaDescription = $this->getCustomMetaDescription();
            $description = $this->currentCategory->getMetaDescription();
            if ($customMetaDescription) {
                $this->pageConfig->setDescription($customMetaDescription);
            } else if ($description) {
                $this->pageConfig->setDescription($description);
            }

            $keywords = $this->currentCategory->getMetaKeywords();
            if ($keywords) {
                $this->pageConfig->setKeywords($keywords);
            }
            if ($this->_categoryHelper->canUseCanonicalTag()) {
                $this->pageConfig->addRemotePageAsset(
                    $this->getCanonicalLinkForCustomCategory(),
                    'canonical',
                    ['attributes' => ['rel' => 'canonical']]
                );
            }

            $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
            $usedParams = $this->removeUnwantedParams($this->request->getParams());
            $categoryHeading = '';

            if ($this->hasCustomCategory() === true) {
                $this->pageConfig->setRobots('index, follow');
                $categoryHeading = $this->getCustomHeading();
            } elseif (!empty($usedParams)) {
                $this->pageConfig->setRobots('noindex, follow');
            }

            if ($pageMainTitle && $categoryHeading) {
                $pageMainTitle->setPageTitle($categoryHeading);
            } else {
                $pageMainTitle->setPageTitle($this->currentCategory->getName());
            }
        }
    }
}