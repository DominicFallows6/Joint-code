<?php

namespace Limitless\SalesHotline\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;

class View extends Template
{

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var TimezoneInterface  */
    private $timezone;

    /**
     * View constructor.
     * @param Context $context
     * @param TimezoneInterface $timezone
     * @param array $data
     */
    public function __construct(
        Context $context,
        TimezoneInterface $timezone,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->scopeConfig = $context->getScopeConfig();
        $this->timezone = $timezone;

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

    public function getConfigDropDownValues() {

        $tableConfig = $this->scopeConfig->getValue('general/limitless_sales_hotline/days');

        $tableConfigResults = unserialize($tableConfig);

        return $tableConfigResults;

    }


    public function getTimeAccordingToTimeZone()
    {

        if (($this->timezone instanceof \DateTime) === false) {
            $this->timezone = new \DateTime;
            $this->timezone->setTimeZone(new \DateTimeZone($this->scopeConfig->getValue('general/limitless_sales_hotline/timezone')));
        }

        return $this->timezone;

    }

    public function getTimeZone()
    {
        return $this->getTimeAccordingToTimeZone()->format('T');
    }

    public function getTimeZoneOffset()
    {
        return $this->getTimeAccordingToTimeZone()->format('Z');
    }


    public function getOpeningTimes() {

        $openingTimes = "";

        foreach ($this->daysOfWeek() as $key => $value) {
            $openingTimes .= $this->getOpeningHours($key);
        }

        $openingTimes = explode(',', rtrim($openingTimes, ","));

        return json_encode($openingTimes);

    }

    public function getClosedValue() {

        $openClosed = "";

        foreach ($this->daysOfWeek() as $key => $value) {
            $openClosed .= $this->getOpenClosedValue($key);
        }

        $openClosed = explode(',', rtrim($openClosed, ","));

        return json_encode($openClosed);

    }

    public function getSalesNumber() {

        $salesNumberHtml = "";

        $salesNumber = $this->scopeConfig->getValue('general/limitless_sales_hotline/sales_number');

        $salesNumberCtc = str_replace(' ', '', $salesNumber);

        if($salesNumber != "") {
            $salesNumberHtml = "<a href='tel:$salesNumberCtc' class='sales-number'>" . $salesNumber . "</a>";
        }

        return $salesNumberHtml;
    }

    public function checkInterval() {
        return $this->scopeConfig->getValue('general/limitless_sales_hotline/check_interval');
    }

    public function getOpenText() {
        $openText = $this->scopeConfig->getValue('general/limitless_sales_hotline/open_text');
        return '<span>' . $openText . '</span>';
    }

    public function getClosedText() {
        $closedText = $this->scopeConfig->getValue('general/limitless_sales_hotline/closed_text');
        return '<span>' . $closedText . '</span>';
    }

    public function getHelpCentreLink() {

        $helpCentreLinkHtml = "";

        $helpCentreLink = $this->scopeConfig->getValue('general/limitless_sales_hotline/helpcentre_link');

        if($helpCentreLink != "") {
            $helpCentreLinkHtml = "<a href='" . $helpCentreLink . "' target='_blank' class='help-centre'>Hilfe</a>";
        }

        return $helpCentreLinkHtml;
    }

    public function getOpenClosedValue($int) {

        $openOrClosed = "";

        $i = 0;
        $selectedTimes = $this->getConfigDropDownValues();
        foreach($selectedTimes as $key => $value) {
            if($key === 'open_or_closed') {
                if ($i === 0) {
                    $openOrClosed = $value[$int];
                }
            }
        }

        $openOrClosedTxt = $openOrClosed . ',';

        return $openOrClosedTxt;

    }


    public function getOpeningHours($int) {

        $openTime = "";
        $closedTime = "";

        $i = 0;
        $selectedTimes = $this->getConfigDropDownValues();
        foreach($selectedTimes as $key => $value) {

            if($key === 'open_time') {
                if ($i === 0) {
                    $openTime = $value[$int] . ' - ';
                }
            }

            if($key === 'closed_time') {
                if ($i === 0) {
                    $closedTime = $value[$int];
                }
            }

        }

        $times = $openTime . $closedTime . ',';

        return $times;

    }

}