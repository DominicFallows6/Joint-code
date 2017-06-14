
# Worldpay Order Extensions

This module changes functionality within the Worldpay module:

**CardModify.php**
1) Creates and sends Order Prefix to Worldpay for Order Code
2) Stores Risk Score from Worldpay
3) Reports Risk Score in Payments Extension Attributes from Order Api Call
4) Allows Site Order to use indiviually specificed MOTO siteCode

**WorldpayConfig**
1) Takes Worldpay Configuration from the correct scope 

```
Possible TODO:
Record more info about order process (card country origin and 3DS return type) 
to identify risk score bypass.
Possibly move WorldpayOrderExtensions into this module
```

