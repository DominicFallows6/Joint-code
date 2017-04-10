<?php

declare(strict_types=1);

namespace Limitless\ProductDefaultVisibility\Setup;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    const NOT_VISIBLE = Visibility::VISIBILITY_NOT_VISIBLE;

    /**
     * @var EavSetup
     */
    private $eavSetup;

    public function __construct(EavSetup $eavSetup)
    {
        $this->eavSetup = $eavSetup;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->eavSetup->updateAttribute(Product::ENTITY, 'visibility', 'default_value', self::NOT_VISIBLE);
    }
}
