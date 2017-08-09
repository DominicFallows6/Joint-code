<?php

namespace Limitless\ProductPDF\Plugin;

use Limitless\ProductPDF\Block\View;
use Magento\Catalog\Block\Product\View\Attributes;

class AttributesOutputPlugin
{
    /** @var View */
    private $productPdfView;

    public function __construct(View $productPdfView)
    {
        $this->productPdfView = $productPdfView;
    }

    public function afterToHtml(Attributes $subject, string $html)
    {
        $pdfHtmlAppend = $this->productPdfView->getPdfHtml();
        return $html . $pdfHtmlAppend;
    }
}