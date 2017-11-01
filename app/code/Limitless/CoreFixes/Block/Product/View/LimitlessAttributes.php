<?php

namespace Limitless\CoreFixes\Block\Product\View;

use Magento\Catalog\Block\Product\View\Attributes as Magento2Attributes;

class LimitlessAttributes extends Magento2Attributes
{

    /**
     * $excludeAttr is optional array of attribute codes to
     * exclude them from additional data array
     *
     * @param array $excludeAttr
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getAdditionalData(array $excludeAttr = [])
    {
        $data = [];
        $product = $this->getProduct();
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
                $value = $attribute->getFrontend()->getValue($product);

                if (!$product->hasData($attribute->getAttributeCode())) {
                    $value = __('N/A');
                } elseif ($value instanceof Phrase) {
                    $value = (string)$value;
                } elseif ((string)$value == '') {
                    $value = __('No');
                } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                    $value = $this->priceCurrency->convertAndFormat($value);
                }

                //only adds if string
                if (is_string($value) && strlen($value)) {
                    $data[$attribute->getAttributeCode()] = [
                        'label' => __($attribute->getStoreLabel()),
                        'value' => $value,
                        'code' => $attribute->getAttributeCode(),
                    ];
                }
            }
        }

        return $data;
    }

    protected function _toHtml()
    {
        $this->setModuleName($this->extractModuleName('Magento\Catalog\Block\Product\View\Attributes'));
        return parent::_toHtml();
    }

}