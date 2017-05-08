<?php
/**
 * Created by PhpStorm.
 * User: tprocter
 * Date: 14/04/2017
 * Time: 11:03
 */

namespace Limitless\CheckoutPaymentInstructions\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\OfflinePayments\Model\Banktransfer;
use Magento\Payment\Helper\Data as PaymentHelper;

class AdditionalConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string[]
     */
    protected $methodCodes = [
        Banktransfer::PAYMENT_METHOD_BANKTRANSFER_CODE,
    ];

    /**
     * @var \Magento\Payment\Model\Method\AbstractMethod[]
     */
    protected $methods = [];

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @param PaymentHelper $paymentHelper
     * @param Escaper $escaper
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        Escaper $escaper
    ) {
        $this->escaper = $escaper;
        foreach ($this->methodCodes as $code) {
            $this->methods[$code] = $paymentHelper->getMethodInstance($code);
        }
    }


    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [];
        foreach ($this->methodCodes as $code) {
            if ($this->methods[$code]->isAvailable()) {
                $config['payment']['checkout_instructions'][$code] = $this->getCheckoutInstructions($code);
            }
        }
        return $config;
    }
    /**
     * Get checkout instructions text from config
     *
     * @param string $code
     * @return string
     */
    protected function getCheckoutInstructions($code)
    {
        return nl2br($this->escaper->escapeHtml($this->methods[$code]->getCheckoutInstructions()));
    }
}