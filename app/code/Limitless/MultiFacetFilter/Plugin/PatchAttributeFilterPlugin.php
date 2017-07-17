<?php

namespace Limitless\MultiFacetFilter\Plugin;

use Magento\Catalog\Model\Layer\Filter\ItemFactory as FilterItemFactory;
use Magento\CatalogSearch\Model\Layer\Filter\Attribute as AttributeFilter;
use Magento\Framework\App\RequestInterface;

class PatchAttributeFilterPlugin
{
    /**
     * @var FilterItemFactory
     */
    private $filterItemFactory;

    public function __construct(FilterItemFactory $filterItemFactory)
    {
        $this->filterItemFactory = $filterItemFactory;
    }
    
    public function aroundApply(AttributeFilter $subject, \Closure $proceed, RequestInterface $request)
    {
        $requestVar = $subject->getAttributeModel()->getAttributeCode();
        $attributeValue = $request->getParam($requestVar);
        if (empty($attributeValue)) {
            return $subject;
        }

        $attribute = $subject->getAttributeModel();
        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $subject->getLayer()->getProductCollection();
        $productCollection->addFieldToFilter($attribute->getAttributeCode(), $attributeValue);
        
        $valuesToAdd = is_array($attributeValue) ? $attributeValue : [$attributeValue];

        foreach ($valuesToAdd as $value) {
            $label = $subject->getAttributeModel()->getFrontend()->getOption($value);
            $filterItem = $this->createItem($subject, $label, $value);
            $subject->getLayer()
                ->getState()
                ->addFilter($filterItem);
        }

        return $subject;
    }

    private function createItem($filter, $label, $value)
    {
        return $this->filterItemFactory->create()
            ->setFilter($filter)
            ->setLabel($label)
            ->setValue($value)
            ->setCount(0);
    }
}
