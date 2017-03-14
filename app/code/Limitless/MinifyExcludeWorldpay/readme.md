The purpose of this module is to exclude the worldpay js file from being minified as laid out in TP #7382. 

Worldpay do not provide extra files such as worldpay.min.js so in this modules config.xml we are excluding it.

There is an open issue on Github for this https://github.com/magento/magento2/issues/5835. 