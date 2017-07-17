<?php


namespace Limitless\Elastic\Model\Layer;

use Magento\Catalog\Model\Layer\State as StateParent;

class State extends StateParent
{

    private $request;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ){
        parent::__construct($data);
        $this->request = $request;
    }

    public function getFilters()
    {
        $filters = $this->getData('filters');
        if ($filters === null) {
            $filters = [];
            $this->setData('filters', $filters);
        }
        return $filters;
    }

    /**
     * @return bool
     */
    public function hasPriceFilter()
    {
        $param = $this->request->getParam('price');

        if ($param) {
            return true;
        } else {
            return false;
        }
    }

}