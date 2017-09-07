<?php


namespace Limitless\Elastic\Block;

use Magento\LayeredNavigation\Block\Navigation\State;
use Limitless\Elastic\Helpers\ArrayHelper;

class PriceFilters extends State
{

    protected $_template = 'Limitless_Elastic::layer/state.phtml';

    /**
     * @var bool $arePriceFiltersRequired Flag to check if Price Filters are Required
     */
    private $arePriceFiltersRequired = false;

    /**
     * @var array $priceFilters Array of Price Filter Aggregations
     */
    private $priceFilters = [];
    private $pricingHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Directory\Model\Currency $pricingHelper,
        array $data = []
    ){

        parent::__construct($context, $layerResolver, $data);

        $this->pricingHelper = $pricingHelper;
        $this->initialisePriceFilters();
    }

    /**
     * @return bool
     */
    public function arePriceFiltersRequired()
    {
        return $this->arePriceFiltersRequired;
    }

    protected function initialisePriceFilters()
    {
        $params = $this->_request->getParams();
        $params = ArrayHelper::ensureMagento2Based2DArray($params);

        if (!empty($params['price'])) {
            $this->arePriceFiltersRequired = true;
            foreach ($params['price'] as $key => $value) {
                $this->priceFilters[] = $this->buildFacetedLink('price', $value, $params, $this->_request->getRequestString());
            }
        }
    }

    public function getPriceFilters() : array
    {
        return $this->priceFilters;
    }

    private function buildFacetedLink(string $key, string $value, array $queryArray, string $url) : array
    {

        $result = [];
        $result['removal_link'] = 0;
        $result['original_value'] = $value;
        $result['display_value'] = implode(' ', $this->createFullyFormedCurrencyRangeLabel($value));

        if (isset($queryArray[$key])) {
            if (is_array($queryArray[$key]) && !in_array($value, $queryArray[$key])) {
                $queryArray[$key][] = $value;
            } else {
                $keyToRemove = array_search($value, $queryArray[$key]);
                unset($queryArray[$key][$keyToRemove]);
                $result['removal_link'] = 1;
            }
        } else {
            $queryArray[$key][] = $value;
        }

        $result['url'] = $url.'?'.http_build_query($queryArray);

        return $result;

    }

    protected function createFullyFormedCurrencyRangeLabel(string $value) : array
    {
        $values = explode( '-', $value);
        $returnValues = [];

        if ($values[0] == '') {
            $returnValues[0] = $this->pricingHelper->format('0.00', [], false).' - ';
        } else {
            $returnValues[0] = $this->pricingHelper->format($values[0],  [], false);
        }

        if ($values[1] == '') {
            $returnValues[1] = __('%1 and above', '');
        } else {

            if ($values[0] == '') {
                $returnValues[1] = $this->pricingHelper->format($values[1]-0.01,  [], false);
            } else {
                $returnValues[1] = ' - '.$this->pricingHelper->format($values[1]-0.01,  [], false);
            }
        }

        return $returnValues;

    }

    /**
     * Retrieve Clear Filters URL
     *
     * @return string
     */
    public function getClearUrl()
    {
        $filterState = [];

        foreach ($this->getActiveFilters() as $item) {
            $filterState[$item->getFilter()->getRequestVar()] = $item->getFilter()->getCleanValue();
        }

        $parametersUsed = $this->_request->getParams();

        if (!empty($parametersUsed['price'])) {
            $filterState['price'] = null;
        }

        if (!empty($parametersUsed['id'])) {
            $filterState['id'] = null;
        }

        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $filterState;
        $params['_escape'] = true;
        $url = $this->_urlBuilder->getUrl('*/*/*', $params);

        return $url;
    }

}