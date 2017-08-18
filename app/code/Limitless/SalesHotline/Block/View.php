<?php

namespace Limitless\SalesHotline\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\ScopeInterface;
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
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->scopeConfig = $context->getScopeConfig();
    }

    private function getScopeConfigValue($path)
    {
        return $this->scopeConfig->getValue('general/limitless_sales_hotline/' . $path, ScopeInterface::SCOPE_STORE);
    }

    public function getOpeningTimes()
    {
        $openingTimes = "";

        foreach ($this->getOpeningTimesConfig() as $key => $value) {
            $openingTime = str_replace(',',':', substr($value['opening_time'], 0, 5));
            $closedTime = str_replace(',',':', substr($value['closed_time'], 0, 5));
            $openingTimes .= $openingTime . ' - ' . $closedTime . ',';
        }

        $openingTimes = explode(',', rtrim($openingTimes, ","));

        return json_encode($openingTimes);
    }

    public function getClosedValue()
    {
        $closedValues = "";

        foreach ($this->getOpeningTimesConfig() as $key => $value)
        {
            $closedValues .= $value['open_or_closed'] . ',';
        }

        $closedValues = explode(',', rtrim($closedValues, ","));

        return json_encode($closedValues);
    }

    public function getTimeAccordingToTimeZone()
    {
        if (($this->timezone instanceof \DateTime) === false) {
            $this->timezone = new \DateTime;
            $this->timezone->setTimeZone(new \DateTimeZone($this->getScopeConfigValue('timezone')));
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

    public function getSalesNumber()
    {
        $salesNumberHtml = "";
        $salesNumber = $this->getScopeConfigValue('sales_number');
        $salesNumberCtc = str_replace(' ', '', $salesNumber);

        if ($salesNumber != "") {
            $salesNumberHtml = "<a href='tel:$salesNumberCtc' class='sales-number'>" . $salesNumber . "</a>";
        }

        return $salesNumberHtml;
    }

    public function checkInterval()
    {
        return $this->getScopeConfigValue('check_interval');
    }

    public function getOpenText()
    {
        $openText = $this->getScopeConfigValue('open_text');
        return '<span>' . $openText . '</span>';
    }

    public function getClosedText()
    {
        $closedText = $this->getScopeConfigValue('closed_text');
        return '<span>' . $closedText . '</span>';
    }

    public function getHelpCentreLink()
    {
        $helpCentreLinkHtml = "";
        $helpCentreText = $this->getScopeConfigValue('helpcentre_text');
        $helpCentreLink = $this->getScopeConfigValue('helpcentre_link');

        if ($helpCentreLink != "") {
            $helpCentreLinkHtml = "<a href='" . $helpCentreLink . "' target='_blank' class='help-centre'>" . $helpCentreText . "</a>";
        }

        return $helpCentreLinkHtml;
    }

    public function getOpeningTimesConfig():array
    {
        return [
            [
                "opening_time" => $this->getScopeConfigValue('sunday_opening_time'),
                "closed_time" => $this->getScopeConfigValue('sunday_closed_time'),
                "open_or_closed" => $this->getScopeConfigValue('sunday_open')
            ],
            [
                "opening_time" => $this->getScopeConfigValue('monday_opening_time'),
                "closed_time" => $this->getScopeConfigValue('monday_closed_time'),
                "open_or_closed" => $this->getScopeConfigValue('monday_open')
            ],
            [
                "opening_time" => $this->getScopeConfigValue('tuesday_opening_time'),
                "closed_time" => $this->getScopeConfigValue('tuesday_closed_time'),
                "open_or_closed" => $this->getScopeConfigValue('tuesday_open')
            ],
            [
                "opening_time" => $this->getScopeConfigValue('wednesday_opening_time'),
                "closed_time" => $this->getScopeConfigValue('wednesday_closed_time'),
                "open_or_closed" => $this->getScopeConfigValue('wednesday_open')
            ],
            [
                "opening_time" => $this->getScopeConfigValue('thursday_opening_time'),
                "closed_time" => $this->getScopeConfigValue('thursday_closed_time'),
                "open_or_closed" => $this->getScopeConfigValue('thursday_open')
            ],
            [
                "opening_time" => $this->getScopeConfigValue('friday_opening_time'),
                "closed_time" => $this->getScopeConfigValue('friday_closed_time'),
                "open_or_closed" => $this->getScopeConfigValue('friday_open')
            ],
            [
                "opening_time" => $this->getScopeConfigValue('saturday_opening_time'),
                "closed_time" => $this->getScopeConfigValue('saturday_closed_time'),
                "open_or_closed" => $this->getScopeConfigValue('saturday_open')
            ]
        ];
    }
}