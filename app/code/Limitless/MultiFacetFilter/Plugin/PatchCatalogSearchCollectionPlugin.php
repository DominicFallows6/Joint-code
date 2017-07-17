<?php

namespace Limitless\MultiFacetFilter\Plugin {

    use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection;

    class PatchCatalogSearchCollectionPlugin
    {

        public static $lastField;

        public function beforeAddFieldToFilter(Collection $subject, $field, $condition = null)
        {

            self::$lastField = $field;

            return [$field, $condition];
        }
    }
}

/*
 * This is a workaround for the method
 * \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection::addFieldToFilter
 * It calls in_array() without the $strict flag set to true, causing a needle of 0 (zero)
 * to match non-numeric values in $haystack (e.g. ['from', 'to']).
 */
namespace Magento\CatalogSearch\Model\ResourceModel\Fulltext {

    use Limitless\MultiFacetFilter\Plugin\PatchCatalogSearchCollectionPlugin;

    function in_array($needle, array $haystack, $strict = null)
    {
        if (null === $strict) {
            $strict = PatchCatalogSearchCollectionPlugin::$lastField !== 'visibility';
        }
        
        //reset
        PatchCatalogSearchCollectionPlugin::$lastField = null;
        
        return \in_array($needle, $haystack, $strict);
    }
}
