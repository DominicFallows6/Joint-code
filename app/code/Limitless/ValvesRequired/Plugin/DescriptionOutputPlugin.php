<?php

namespace Limitless\ValvesRequired\Plugin;

use Limitless\ValvesRequired\Block\View;
use Magento\Catalog\Block\Product\View\Description;

class DescriptionOutputPlugin
{
    /** @var View */
    private $productValvesView;
    public function __construct(View $productValvesView)
    {
        $this->productValvesView = $productValvesView;
    }
    public function afterToHtml(Description $subject, string $html)
    {
        if ($subject->getNameInLayout() === 'product.info.description') {
            if ($this->productValvesView->hasValvesRequired() ) {
                $valvesCta = __('Valves not included') . ' - <a href="#" class="valves-required">' . __('Which Valves do I need?') . '</a>';
                $valvesHtmlBlockAppend = $this->productValvesView->getValvesRequiredHtml();
                $html .= $valvesCta . '<div id="valves-pop-up" style="display:none;">' . $valvesHtmlBlockAppend . '</div>';
            }
        }
        return $html;
    }
}