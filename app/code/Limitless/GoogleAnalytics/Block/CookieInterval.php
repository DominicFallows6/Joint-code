<?php

namespace Limitless\GoogleAnalytics\Block;

use Magento\Framework\View\Element\Template;

class CookieInterval extends Template
{
    //Todo get interval check time
    public function __construct(Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }
}