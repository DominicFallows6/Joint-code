1. There is a core Magento issue where you aren't allowed to enter your country prefix in front of the VAT number, since it uses the country from your address. 
2. There is a core Magento issue where the cart/minicart totals don't update when you add a valid/invalid vat number in the customer address. This module adds a plugin for the afterSave event on the customer address form. A preference is also included which removes the check to see if the customer vat number is null/false. This way, the customer vat number is always set from the customer repository (if they have a customer id) or from the customer session. 

Link to the issue on Github for future reference - https://github.com/magento/magento2/issues/10567

This module adds a translatable message to the VAT text field in the following areas:
- Shipping and billing address forms in the frontend checkout
- "Create New Order" address form in admin
- Address form in customer account on the frontend 

Magento do not acknowledge the country prefix issue as a bug and instead have moved the issue to the "Feature requests" log.  

https://community.magento.com/t5/Magento-2-Feature-Requests-and/EU-VAT-number-validation-improvement-suggestions/idi-p/47272
https://community.magento.com/t5/Magento-2-Feature-Requests-and/Allow-VAT-number-to-contain-country-code/idi-p/45705

The custom message is added to the frontend of the checkout by a preference on the AttributeMerger class which overrides two protected functions. 

To show our custom VAT message the conditions are:
- attribute code is 'vat_id'
- "Show VAT Number on Storefront" setting being configured to "Yes" in admin
- Customer must be logged in

The custom message is shown in the admin address form by a preference on the Vat class which renders our custom vat.phtml template.
 
The custom message is shown in the customer account section on the frontend via setting a new template in the xml.
 
