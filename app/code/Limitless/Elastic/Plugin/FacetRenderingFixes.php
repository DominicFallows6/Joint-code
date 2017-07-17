<?php

namespace Limitless\Elastic\Plugin;

use Limitless\Elastic\Plugin\Util\FacetUrlBuilder;
use Magento\Framework\App\RequestInterface;

class FacetRenderingFixes
{

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;
    /**
     * @var \Magento\Theme\Block\Html\Pager
     */
    private $htmlPagerBlock;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var FacetUrlBuilder
     */
    private $facetUrlBuilder;

    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Theme\Block\Html\Pager $htmlPagerBlock,
        RequestInterface $request,
        FacetUrlBuilder $facetUrlBuilder
    ) {
        $this->url = $url;
        $this->htmlPagerBlock = $htmlPagerBlock;
        $this->request = $request;
        $this->facetUrlBuilder = $facetUrlBuilder;
    }

    public function aroundGetUrl(\Magento\Catalog\Model\Layer\Filter\Item $subject, \Closure $closure)
    {
        $query = [
            $subject->getFilter()->getRequestVar() . '[]' => $subject->getValue(),
            // exclude current page from urls
            $this->htmlPagerBlock->getPageVarName() => null,
        ];
        return $this->url->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }

    public function around__call(\Magento\Catalog\Model\Layer\Filter\Item $subject, \Closure $proceed, $method, $args)
    {
        if ($method === 'getIsSelected') {
            $getParam = $this->request->getParam($subject->getFilter()->getRequestVar(), []);

            return is_array($getParam) && in_array($subject->getValue(), $getParam) || $getParam == $subject->getValue();

        } else {
            return $proceed($method, $args);
        }
    }

    public function afterGetRemoveUrl(\Magento\Catalog\Model\Layer\Filter\Item $subject, $result)
    {
        $requestVar = $subject->getFilter()->getRequestVar();

        $params = $this->request->getParams();
        $valueToRemove = $subject->getValue();

        $params = $this->facetUrlBuilder->removeValueFromParams($params, $requestVar, $valueToRemove);

        $newParams = $this->facetUrlBuilder->getNewQueryParams($params);

        $urlParams = [
            '_use_rewrite' => true,
            '_query' => $newParams,
            '_escape' => true,
        ];

        return $this->url->getUrl('*/*/*', $urlParams);
    }

}