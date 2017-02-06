<?php

namespace Limitless\SalesHotline\Block\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Custom extends Field
{

    /**
     * @var ScopeConfigInterface
     */

    private $scopeConfig;

    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $context->getScopeConfig();
    }


    public function getConfigDropDownValues() {

        $tableConfig = $this->scopeConfig->getValue('general/limitless_sales_hotline/days', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);

        $tableConfigResults = unserialize($tableConfig);

        return $tableConfigResults;

    }

    private function daysOfWeek()
    {
        return array(
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday'
        );
    }

    protected function _getElementHtml(AbstractElement $element) {

        $html = '<table id="opening-times">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Day of week</th>';
        $html .= '<th>Open Time</th>';
        $html .= '<th>Closed Time</th>';
        $html .= '<th>Closed?</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($this->daysOfWeek() as $key => $value) {
            $html .= '<tr>';
            $html .= '<td><input name="'. $element->getName() . '['.$key.']' . '" id="'. $element->getId() . '" type="text" value="' . $value .'" disabled="disabled"/></td>';
            $html .= '<td><select name="' . $element->getName() . '[' . "open_time" . ']' . '[' . $key . ']' . '" id="' . $element->getId() . '">' . $this->getTimesHtml($key,'open_time') . '</select></td>';
            $html .= '<td><select name="'. $element->getName() . '['."closed_time".']' . '['.$key.']' . '" id="'. $element->getId() . '">' . $this->getTimesHtml($key,'closed_time') . '</select></td>';
            $html .= '<td><select name="'. $element->getName() . '['."open_or_closed".']' . '['.$key.']' . '" id="'. $element->getId() . '">' . $this->getClosedOpenHtml($key,'open_or_closed') . '</select></td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';

        $html .= '</table>';

        return $html;
    }

    public function getTimesHtml($int, $type) {

        $selectedValue = $this->getSelectedValues($int, $type);

        $timeHtml = "";

        for($hours=0; $hours<24; $hours++) {
            for ($mins = 0; $mins < 60; $mins += 1) {
                $time = str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($mins, 2, '0', STR_PAD_LEFT);
                if($time === $selectedValue) {
                    $selected = "selected";
                } else {
                    $selected = "";
                }
                $timeHtml .= '<option value="' . $time . '" ' . $selected . '>' . $time . '</option>';
            }
        }

        return $timeHtml;

    }

    public function getClosedOpenHtml($int,$type) {

        $selectedValue = $this->getSelectedValues($int, $type);

        $closed_open_options = "";

        $opening_toggle = array("No", "Yes");

        foreach($opening_toggle as $yes_no){
            if($yes_no === $selectedValue) {
                $selected = "selected";
            } else {
                $selected = "";
            }

            $closed_open_options .= '<option value="' . $yes_no . '"' . $selected .'>' . $yes_no. '</option>';
        }

        return $closed_open_options;
    }


    /**
     * @param $int
     * @param $type
     * @return string
     */
    private function getSelectedValues($int, $type):string
    {
        $selectedValue = "";

        $i = 0;
        $selectedTimes = $this->getConfigDropDownValues();
        foreach ($selectedTimes as $key => $value) {
            if ($key === $type) {
                if ($i === 0) {
                    $selectedValue = $value[$int];
                }
            }
        }
        return $selectedValue;
    }

}