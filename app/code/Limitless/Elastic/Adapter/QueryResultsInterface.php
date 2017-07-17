<?php
/**
 * Created by PhpStorm.
 * User: jfirminger
 * Date: 31/01/2017
 * Time: 09:14
 */

namespace Limitless\Elastic\Adapter;

interface QueryResultsInterface
{
    public function getResults(): array;

    public function getFilters(): array;
}