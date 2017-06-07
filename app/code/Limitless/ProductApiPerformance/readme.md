This module increases the performance of the product API by improving the handling of product links during the save process.

Usually, while a product is being saved, all existing links for that product are first deleted from the database,  
and then all links that are set on the product model being saved are inserted again.  

This happens on every call to `\Magento\Catalog\Model\Product::afterSave()`, regardless if any of the links where changed or not.  

This module changes this behavior that only changed or removed links are deleted, and only changed or new links are saved.  
If no changes where made to the product links no further further link processing happens.

Note:  
The `ProductRepository::save()` method also processes product links, but skips that processing if none are specified   
on the product. It's only the additional link processing which is triggered by the products afterSave() hook method
that always happens.
