<?php

namespace Limitless\Elastic\Plugin;

use Limitless\Elastic\Plugin\Util\FacetUrlBuilder;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Swatches\Block\LayeredNavigation\RenderLayered;

class SwatchesFacetRenderingFixesPlugin
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;
    /**
     * @var FacetUrlBuilder
     */
    private $facetUrlBuilder;
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * SwatchesFacetRenderingFixesPlugin constructor.
     */
    public function __construct(UrlInterface $urlBuilder, FacetUrlBuilder $facetUrlBuilder, RequestInterface $request)
    {
        $this->urlBuilder = $urlBuilder;
        $this->facetUrlBuilder = $facetUrlBuilder;
        $this->request = $request;
    }

    public function aroundBuildUrl(RenderLayered $subject, \Closure $proceed, $attributeCode, $optionId)
    {
        $filter = $this->extractFilter($subject);
        $requestVar = $filter->getRequestVar();

        $origParams = $this->request->getParams();
        $currentParams = $this->facetUrlBuilder->removeValueFromParams($origParams, $requestVar, $filter->getValue());
        if (! isset($currentParams[$requestVar]) || ! is_array($currentParams[$requestVar])) {
            $currentParams[$requestVar] = [];
        }
        $currentParams[$requestVar][count($currentParams[$requestVar])] = $optionId;
        $newQueryParams = $this->facetUrlBuilder->getNewQueryParams($currentParams);
        return $this->urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $newQueryParams]);
    }

    private function extractFilter(RenderLayered $subject):AbstractFilter
    {
        $property = new \ReflectionProperty($subject, 'filter');
        $property->setAccessible(true);

        /** @var AbstractFilter $filter */
        $filter = $property->getValue($subject);
        return $filter;
    }

    public function afterGetSwatchData(RenderLayered $subject, $result)
    {
        $param = $this->request->getParam($result['attribute_code']);

        if ($param) {

            if (!is_array($param)) {
                $param = [$param];
            }

            foreach ($param as $value) {
                if (isset($result['options'][$value])) {
                    unset($result['options'][$value]);
                }
            }

        }

        return $result;
    }

}