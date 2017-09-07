<?php

namespace Limitless\Elastic\Helpers;

class ArrayHelper
{
    /**
     * This Helper function has been added to cope with the old BackOffice SEO problems
     */
    static public function ensureMagento2Based2DArray(
        array $params,
        array $ignoredElements = ['q', 'p', 'product_list_limit', 'product_list_order'],
        array $removalElements = ['pg', 'id']
    ) {
        $returnElements = [];

        foreach ($params as $keyParam => $valueParam) {
            if (in_array($keyParam, $removalElements)) {
                continue;
            } elseif (in_array($keyParam, $ignoredElements)) {
                $returnElements[$keyParam] = $valueParam;
            } elseif (is_array($valueParam)) {
                $returnElements[$keyParam] = array_values($valueParam);
            } else {
                $returnElements[$keyParam][] = $valueParam;
            }
        }

        return $returnElements;
    }
}