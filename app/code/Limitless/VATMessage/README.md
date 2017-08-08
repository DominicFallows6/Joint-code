This module adds a translatable message to the VAT text field in the following areas:
- Shipping and billing address forms in the frontend checkout
- "Create New Order" address form in admin
- Address form in customer account on the frontend 

The reason for this module is that there is a core Magento issue where you aren't allowed to enter your country prefix in front of the VAT number, since it uses the country from your address. 

Magento do not acknowledge this as a bug and instead have moved the issue to the "Feature requests" log.  

https://community.magento.com/t5/Magento-2-Feature-Requests-and/EU-VAT-number-validation-improvement-suggestions/idi-p/47272
https://community.magento.com/t5/Magento-2-Feature-Requests-and/Allow-VAT-number-to-contain-country-code/idi-p/45705

The custom message is added to the frontend of the checkout by a preference on the AttributeMerger class which overrides two protected functions. 

To show our custom VAT message the conditions are:
- attribute code is 'vat_id'
- "Show VAT Number on Storefront" setting being configured to "Yes" in admin
- Customer must be logged in

The custom message is shown in the admin address form by a preference on the Vat class which renders our custom vat.phtml template.
 
The custom message is shown in the customer account section on the frontend via setting a new template in the xml. 