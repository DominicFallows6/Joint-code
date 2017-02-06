<?php

namespace Limitless\ReasonsUSP\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class View extends Template
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

    private function getConfig($path)
    {
        return $this->scopeConfig->getValue('general/limitless_reasons_usp/' . $path);
    }

    public function getIcon($path) {
        return $this->getBaseUrl() . 'media/usp/' . $this->getConfig($path);
    }

    public function getReasonsToBuy()
    {

        $usps_html = "";

        foreach ($this->getUspConfig() as $key => $value) {

            $icon = $this->getIconHtml($value);

            $tip = $this->getTipHtml($value);

            $site_usp = $this->buildUspItemHtml($value, $icon, $tip);

            $usps_html .= $site_usp;

        }

        return $usps_html;
    }

    private function getIconHtml($value):string
    {
        return $this->hasIconFile($value) ? '<img src="' . $value['usp_icon'] . '" />' : '';
    }

    private function hasIconFile($value):bool
    {
        return substr($value['usp_icon'], -1) !== '/';
    }

    private function getTipHtml($value):string
    {
        return $value['usp_tip'] != '' ? '<span>' . $value['usp_tip'] . '</span>' : '';
    }

    private function buildUspItemHtml($value, $icon, $tip):string
    {
        if ($value['usp_url'] != '') {
            $site_usp = '<li><div class="usp-box"><a href="' . $value['usp_url'] . '">' . $icon . $value['usp_name'] . '</a>' . $tip . '</div></li>';
        } else {
            $site_usp = '<li><div class="usp-box">' . $icon . $value['usp_name'] . $tip . '</div></li>';
        }
        return $site_usp;
    }

    private function getUspConfig():array
    {
        return [
            [
                "usp_name" => $this->getConfig('usp_one'),
                "usp_url" => $this->getConfig('usp_one_url'),
                "usp_tip" => $this->getConfig('usp_one_tip'),
                "usp_icon" => $this->getIcon('usp_one_icon'),
            ],
            [
                "usp_name" => $this->getConfig('usp_two'),
                "usp_url" => $this->getConfig('usp_two_url'),
                "usp_tip" => $this->getConfig('usp_two_tip'),
                "usp_icon" => $this->getIcon('usp_two_icon'),
            ],
            [
                "usp_name" => $this->getConfig('usp_three'),
                "usp_url" => $this->getConfig('usp_three_url'),
                "usp_tip" => $this->getConfig('usp_three_tip'),
                "usp_icon" => $this->getIcon('usp_three_icon'),
            ],
            [
                "usp_name" => $this->getConfig('usp_four'),
                "usp_url" => $this->getConfig('usp_four_url'),
                "usp_tip" => $this->getConfig('usp_four_tip'),
                "usp_icon" => $this->getIcon('usp_four_icon'),
            ]
        ];

    }
}