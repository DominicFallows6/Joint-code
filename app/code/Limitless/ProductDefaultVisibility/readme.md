# Limitless_ProductDefaultVisibility


This module updates the `eav_attribute`  table column `default_value` for the product attribute `visibility`
to `Not Visible Individually`.  

## Background:
Products are created by Solvitt via the API.  

Here Solvitt does not use the API method  
`\Magento\ConfigurableProduct\Api\ConfigurableProductManagementInterface::generateVariation`,
more specifically `\Magento\ConfigurableProduct\Model\ProductVariationsBuilder::create`, but instead
first creates the individual products with the default visibility, then creates the configurable product
and finally assigns the simple products to the configurable product.  

The result is that the simple products are visible individually in the catalog.  

To resolve this the default visibility is changed to "Not Visible Individually" by this module.  
This applies to both simple products and configurable products (and any other product types, too).  

Note that with this module installed, a configurable products visibility will have to be set to "Catalog, Search" after
it is created.


