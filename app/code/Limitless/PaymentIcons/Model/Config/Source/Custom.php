<?php

namespace Limitless\PaymentIcons\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Custom implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'visa', 'label' =>__('Visa')],
            ['value' => 'visa_electron', 'label' =>__('Visa Electron')],
            ['value' => 'mastercard', 'label' =>__('Mastercard')],
            ['value' => 'amex', 'label' =>__('America Express')],
            ['value' => 'maestro', 'label' =>__('Maestro')],
            ['value' => 'paypal', 'label' =>__('Paypal')],
            ['value' => 'amazon', 'label' =>__('Amazon')],
            ['value' => 'bank_transfer_es', 'label' =>__('Bank Transfer ES')],
            ['value' => 'bank_transfer_it', 'label' =>__('Bank Transfer IT')],
            ['value' => 'bank_transfer_de', 'label' =>__('Bank Transfer DE')],
            ['value' => 'klarna', 'label' =>__('Klarna')],
            ['value' => 'giro_pay', 'label' =>__('Giro Pay')],
            ['value' => 'mistercash', 'label' =>__('Mister Cash')],
            ['value' => 'ideal', 'label' =>__('Ideal')],
            ['value' => 'carte_bleue', 'label' =>__('Cart Bleue')],
            ['value' => 'commerzbank', 'label' =>__('Commerzbank')],
            ['value' => 'mastercard_secure_code', 'label' =>__('Mastercard SecureCode')],
            ['value' => 'verified_by_visa', 'label' =>__('Verified by Visa')]
        ];
    }
}