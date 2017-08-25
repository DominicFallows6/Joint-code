<?php

namespace Limitless\NewsletterSubscribeAtCheckout\Model\Config\Source;

use Limitless\NewsletterSubscribeAtCheckout\Helper\Config as Helper;

/**
 * Class SubscribeLayoutProcessor
 */
class SubscribeLayoutProcessor
{
    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @param Helper $helper
     */
    public function __construct(Helper $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        $checkbox = $this->_helper->getConfigModule('checkout_subscribe');

        $checked = $checkbox == 2 ? 0 : 1;
        $visible = $checkbox == 3 ? 0 : 1;
        $changeable = $checkbox == 4 ? 0 : 1;

        if (!$this->_helper->getConfigModule('enabled')) {
            $visible = 0;
        }

        $jsLayoutSubscribe = [
            'components' => [
                'checkout' => [
                    'children' => [
                        'steps' => [
                            'children' => [
                                'shipping-step' => [
                                    'children' => [
                                        'shippingAddress' => [
                                            'children' => [
                                                'customer-email' => [
                                                    'children' => [
                                                        'newsletter-subscribe' => [
                                                            'config' => [
                                                                'checkoutLabel' =>
                                                                    $this->_helper->getConfigModule('checkout_label'),
                                                                'checked' => $checked,
                                                                'visible' => $visible,
                                                                'changeable' => $changeable
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $jsLayout = array_merge_recursive($jsLayout, $jsLayoutSubscribe);

        return $jsLayout;
    }
}
