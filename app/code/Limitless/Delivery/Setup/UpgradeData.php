<?php

namespace Limitless\Delivery\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY,
        'length',
            [
                'type' => 'int',
                'input' => 'text',
                'label' => 'Length',
                'required' => false,
                'user_defined' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Shipping',
            ]
        );

        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY,
            'width',
            [
                'type' => 'int',
                'input' => 'text',
                'label' => 'Width',
                'required' => false,
                'user_defined' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Shipping',
            ]
        );

        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY,
            'height',
            [
                'type' => 'int',
                'input' => 'text',
                'label' => 'Height',
                'required' => false,
                'user_defined' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Shipping',
            ]
        );

        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY,
            'twoman',
            [
                'type' => 'int',
                'input' => 'boolean',
                'label' => 'Two Man Delivery',
                'required' => false,
                'user_defined' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Shipping',
            ]
        );
    }
}