<?php

namespace Limitless\Elastic\Plugin;

use Limitless\Elastic\Helpers\ArrayHelper;
use Limitless\Elastic\Plugin\Util\FacetUrlBuilder;
use Magento\Catalog\Model\Layer\Filter\Item;
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

    public function aroundGetUrl(Item $subject, \Closure $closure)
    {
        $params = ArrayHelper::ensureMagento2Based2DArray($this->request->getParams());
        $requestVar = $subject->getFilter()->getRequestVar();
        $subjectValue = $subject->getValue();

        if ($requestVar === 'price' && strpos($subjectValue,',') !== FALSE) {
            $params['price'] = explode(',', $subjectValue);;
        } else {
            $params[$requestVar][] = $subjectValue;
        }

        $urlParams = [
            '_use_rewrite' => true,
            '_query' => $params,
            '_escape' => true,
        ];

        $newURL = $this->url->getUrl('*/*/*', $urlParams);

        return $newURL;
    }

    public function afterGetRemoveUrl(Item $subject, $result)
    {
        $requestVar = $subject->getFilter()->getRequestVar();

        $params = $this->request->getParams();
        $params = ArrayHelper::ensureMagento2Based2DArray($params);

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

    public function around__call(Item $subject, \Closure $proceed, $method, $args)
    {
        if ($method === 'getIsSelected') {
            $getParam = $this->request->getParam($subject->getFilter()->getRequestVar(), []);

            return is_array($getParam) && in_array($subject->getValue(),
                    $getParam) || $getParam == $subject->getValue();

        } else {
            return $proceed($method, $args);
        }
    }

}