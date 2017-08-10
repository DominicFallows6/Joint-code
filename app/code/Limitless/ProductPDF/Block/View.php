<?php

namespace Limitless\ProductPDF\Block;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\Product;
use Magento\Store\Model\ScopeInterface;

class View extends Template
{
    const FILE_SEPERATOR = '/';
    const PDF_BASE_URL_PATH = 'base_url';
    const PDF_BASE_URL_SECURITY_PATH = 'base_url_security';

    /** @var Product */
    private $product;

    public function __construct(
        Context $context,
        Registry $registry,
        $data = []
    ) {
        parent::__construct($context, $data);

        $this->product = $registry->registry('current_product');
    }

    public function getPdfHtml()
    {
        $pdfHtml = '';

        if ($this->getPdfEnabled()) {
            $pdfList = $this->getAllPdfUrls();

            if (!empty($pdfList)) {
                $pdfHtml .= '<div id="product-pdf">';
                foreach ($pdfList as $name => $location) {
                    $pdfHtml .= '<div class="pdf-list">';
                    $pdfHtml .= '<span class="pdf-img"></span><a href="' . $location . '">' . $name . '</a>';
                    $pdfHtml .= '</div>';
                }
                $pdfHtml .= '</div>';
            }
        }

        return $pdfHtml;
    }

    private function getAllPdfUrls()
    {
        //Get product PDFs from attribute
        $pdfFilenamesList = $this->product->getProductPdfFilenames();
        $pdfUrls = [];

        if (!empty($pdfFilenamesList)) {
            $productPdfPath = $this->buildProductPdfPath();
            $pdfUrls = $this->buildPdfUrls($pdfFilenamesList, $productPdfPath);
        }

        return $pdfUrls;
    }

    private function buildProductPdfPath()
    {
        $baseUrlPath = $this->buildBaseUrlPath();
        return $baseUrlPath . $this->getPdfDefaultUrl() . $this->getPdfWebsiteUrl() . $this->getPdfStoreUrl();
    }

    private function buildPdfUrls($pdfFilenamesList, $pdfPath)
    {
        $pdfs = [];
        $pdfFiles = explode("\n", $pdfFilenamesList);
        foreach ($pdfFiles as $pdfFile)
        {
            $fileDetails = explode(",", $pdfFile, 2);
            if (!empty($fileDetails[1])) {
                $pdfs[$this->generatePdfFilename($fileDetails[1])] = $pdfPath . $fileDetails[0];
            } else {
                $pdfs[$this->generatePdfFilename($fileDetails[0])] = $pdfPath . $fileDetails[0];
            }
        }
        return $pdfs;
    }

    private function generatePdfFilename($fileName)
    {
        if($this->getShowFilenameExtension()) {
            return $fileName;
        } else {
            $fileNamePart = explode(".", $fileName, 2);
            return $fileNamePart[0];
        }
    }

    private function buildBaseUrlPath()
    {
        $baseUrlPath = $this->getPdfBaseUrl();

        if (strcasecmp($baseUrlPath, 'none') === 0) {
            return '';
        } else {
            $path = 'web/' .  $this->getPdfBaseUrlSecurity() . '/' . $baseUrlPath;
            return $this->_scopeConfig->getValue($path, ScopeInterface::SCOPE_WEBSITE);
        }
    }

    private function getConfigValue($path, $scope)
    {
        return $this->_scopeConfig->getValue('catalog/limitless_product_pdf/' . $path . '', $scope);
    }

    private function cleanUrlPath($urlPath)
    {
        if (empty($urlPath)) {
            return '';
        }

        $urlPath = trim($urlPath, self::FILE_SEPERATOR);
        $urlPath = rtrim($urlPath, self::FILE_SEPERATOR);
        return $urlPath . self::FILE_SEPERATOR;
    }

    private function getPdfBaseUrl()
    {
        $scope = ScopeInterface::SCOPE_STORE;
        $baseUrl =  $this->getConfigValue(self::PDF_BASE_URL_PATH, $scope) ?? 'none';
        return $baseUrl;
    }

    private function getPdfBaseUrlSecurity()
    {
        $scope = ScopeInterface::SCOPE_STORE;
        $baseUrlSecurity =  $this->getConfigValue(self::PDF_BASE_URL_SECURITY_PATH, $scope) ?? 'unsecure';
        return $baseUrlSecurity;
    }

    private function getPdfDefaultUrl()
    {
        $scope = ScopeInterface::SCOPE_WEBSITE;
        $baseUrl =  $this->getConfigValue('pdf_default_address', $scope) ?? '';
        return $this->cleanUrlPath($baseUrl);
    }

    private function getPdfWebsiteUrl()
    {
        $scope = ScopeInterface::SCOPE_WEBSITE;
        $websiteUrl = $this->getConfigValue('pdf_website_address', $scope) ?? '';
        return $this->cleanUrlPath($websiteUrl);
    }

    private function getPdfStoreUrl()
    {
        $scope = ScopeInterface::SCOPE_STORE;
        $storeUrl = $this->getConfigValue('pdf_store_address', $scope) ?? '';
        return $this->cleanUrlPath($storeUrl);
    }

    private function getShowFilenameExtension()
    {
        $scope = ScopeInterface::SCOPE_STORE;
        return $this->getConfigValue('filename_extensions', $scope) ?? '0';
    }

    private function getPdfEnabled()
    {
        $scope = ScopeInterface::SCOPE_STORE;
        return $this->getConfigValue('enabled', $scope) ?? '0';
    }
}