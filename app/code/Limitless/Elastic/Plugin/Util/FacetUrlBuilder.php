<?php

namespace Limitless\Elastic\Plugin\Util;

class FacetUrlBuilder
{
    public function getNewQueryParams(array $params):array
    {
        $newParams = [];
        foreach ($params as $paramName => $value) {
            if (is_array($value)) {
                foreach ($value AS $i => $v) {
                    $newParams[$paramName . '[' . $i . ']'] = $v;
                }
            } else {
                $newParams[$paramName] = $value;
            }
        }
        return $newParams;
    }

    public function removeValueFromParams($params, $requestVar, $valueToRemove)
    {
        if (! is_null($valueToRemove)) {
            if (!isset($params[$requestVar]) || (is_array($params[$requestVar]) && count($params[$requestVar]) == 1)) {
                unset($params[$requestVar]);
            } else {
                $params[$requestVar] = array_filter($params[$requestVar], function ($v) use ($valueToRemove) {
                    return $v != $valueToRemove;
                });
            }
        }
        return $params;
    }
}