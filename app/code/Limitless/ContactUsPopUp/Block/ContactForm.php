<?php

namespace Limitless\ContactUsPopUp\Block;

use Limitless\AliasCode\Block\View as Alias;
use Magento\Contact\Block\ContactForm as ContactFormParent;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Main contact form block
 */
class ContactForm extends Template
{
    /**
     * @var ContactFormParent
     */
    public $contactForm;

    /**
     * @var Alias
     */
    public $alias;

    public function __construct(
        Context $context,
        ContactFormParent $contactForm,
        Alias $alias,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->contactForm = $contactForm;
        $this->alias = $alias;
    }

    /**
     * Returns alias code of product from Limitless_AliasCode
     *
     * @return string
     */
    public function getAliasCode()
    {
        return $this->alias->getAliasCode();
    }

    /**
     * Returns original action url for contact form
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->contactForm->getFormAction();
    }
}