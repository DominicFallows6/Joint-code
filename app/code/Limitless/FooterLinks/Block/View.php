<?php

namespace Limitless\FooterLinks\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;

class View extends Template
{

    //These could be added to config and then use getter
    const TITLE_SPLIT_DELIMITER = '%%%';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    private $maxTotalFooterItems;
    private $maxTotalTitlesPerColumn;

    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $context->getScopeConfig();
    }

    public function getFooterLinksByBlock()
    {
        $rawFooterBlock = $this->getLinkBlockValue();
        $footerLink = '';
        $this->maxTotalFooterItems = $this->getMaxFooterItems();
        $this->maxTotalTitlesPerColumn = $this->getMaxTitlesPerColumn();

        $footerBlockArray = explode("\n", $rawFooterBlock);

        //1. always start with a header
        $headerSet = false;
        $currentTitleCountMet = false;
        $currentTotalCount = 0;
        $currentTitleCount = 0;

        foreach($footerBlockArray as $footerItem)
        {
            if (strpos($footerItem, self::TITLE_SPLIT_DELIMITER) === false) {
                //This is a header
                $headerHtml = $this->getFooterHeaderHtml($footerItem, $currentTotalCount);
                if (false !== $headerHtml) {

                    $footerLink .= $headerHtml;
                    $headerSet = true;
                    $currentTitleCountMet = false;
                    $currentTitleCount = 0;
                    $currentTotalCount++;
                }

            } else {
                if (!$currentTitleCountMet) {
                    $titleParts = explode(self::TITLE_SPLIT_DELIMITER, $footerItem, 2);
                    if ($headerSet && isset($titleParts[0]) && isset($titleParts[1])) {

                        $titleHtml = $this->getFooterTitleAndLinkHtml($titleParts, $currentTitleCount);
                        if (false !== $titleHtml) {

                            $footerLink .= $titleHtml;
                            $currentTitleCount++;
                            $currentTotalCount++;
                        } else {
                            $currentTitleCountMet = true;
                        }
                    }
                }
            }

            if($this->maxTotalFooterItems != 0 && $currentTotalCount >= $this->maxTotalFooterItems) {
                break;
            }
        }

        return $footerLink;
    }

    private function getScopeConfigValue($path)
    {
        return $this->scopeConfig->getValue('general/limitless_footer_links/' . $path, ScopeInterface::SCOPE_STORE);
    }

    private function getLinkBlockValue()
    {
        return $this->getScopeConfigValue('links_block');
    }

    private function getMaxFooterItems()
    {
        $maxItems = $this->getScopeConfigValue('max_footer_items');
        if(is_numeric($maxItems) && $maxItems > 0) {
            return $maxItems;
        } else {
            return 0;
        }
    }

    private function getMaxTitlesPerColumn()
    {
        $maxTitles = $this->getScopeConfigValue('max_footer_titles_per_column');
        if(is_numeric($maxTitles) && $maxTitles > 0) {
            return $maxTitles;
        } else {
            return 0;
        }
    }

    private function getFooterHeaderHtml($header, $currentFooterTotal)
    {
        if ($this->maxTotalFooterItems == 0 || $currentFooterTotal < $this->maxTotalFooterItems) {
            return '</ul></li></ul><ul><li class="hdr">' . trim($header) . '</li><li class="footer-toggle"><ul>';
        }
        return false;
    }

    private function getFooterTitleAndLinkHtml($titleParts, $currentFooterTitleTotal)
    {
        $title = trim($titleParts[0]);
        $link = trim($titleParts[1]);

        if ($this->maxTotalTitlesPerColumn == 0 || $currentFooterTitleTotal < $this->maxTotalTitlesPerColumn) {
            return '<li><a href="' . $link . '" title="' . $title . '">' . $title . '</a></li>';
        }
        return false;
    }
}