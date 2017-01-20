<?php
return array (
  'scopes' => 
  array (
    'websites' => 
    array (
      'admin' => 
      array (
        'website_id' => '0',
        'code' => 'admin',
        'name' => 'Admin',
        'sort_order' => '0',
        'default_group_id' => '0',
        'is_default' => '0',
      ),
      'mageadmin' => 
      array (
        'website_id' => '1',
        'code' => 'mageadmin',
        'name' => 'MageAdmin',
        'sort_order' => '0',
        'default_group_id' => '1',
        'is_default' => '1',
      ),
      'hudsonreed_de' => 
      array (
        'website_id' => '2',
        'code' => 'hudsonreed_de',
        'name' => 'HudsonReed Germany',
        'sort_order' => '1',
        'default_group_id' => '2',
        'is_default' => '0',
      ),
      'hudsonreed_it' => 
      array (
        'website_id' => '3',
        'code' => 'hudsonreed_it',
        'name' => 'HudsonReed Italy',
        'sort_order' => '1',
        'default_group_id' => '3',
        'is_default' => '0',
      ),
      'hudsonreed_es_es' => 
      array (
        'website_id' => '4',
        'code' => 'hudsonreed_es_es',
        'name' => 'HudsonReed Spain',
        'sort_order' => '1',
        'default_group_id' => '4',
        'is_default' => '0',
      ),
    ),
    'groups' => 
    array (
      0 => 
      array (
        'group_id' => '0',
        'website_id' => '0',
        'name' => 'Default',
        'root_category_id' => '0',
        'default_store_id' => '0',
      ),
      2 => 
      array (
        'group_id' => '2',
        'website_id' => '2',
        'name' => 'HudsonReed Germany',
        'root_category_id' => '12',
        'default_store_id' => '2',
      ),
      3 => 
      array (
        'group_id' => '3',
        'website_id' => '3',
        'name' => 'HudsonReed Italy',
        'root_category_id' => '2',
        'default_store_id' => '3',
      ),
      4 => 
      array (
        'group_id' => '4',
        'website_id' => '4',
        'name' => 'HudsonReed Spain',
        'root_category_id' => '2',
        'default_store_id' => '4',
      ),
      1 => 
      array (
        'group_id' => '1',
        'website_id' => '1',
        'name' => 'MageAdmin Store',
        'root_category_id' => '6',
        'default_store_id' => '1',
      ),
    ),
    'stores' => 
    array (
      'admin' => 
      array (
        'store_id' => '0',
        'code' => 'admin',
        'website_id' => '0',
        'group_id' => '0',
        'name' => 'Admin',
        'sort_order' => '0',
        'is_active' => '1',
      ),
      'mageadmin' => 
      array (
        'store_id' => '1',
        'code' => 'mageadmin',
        'website_id' => '1',
        'group_id' => '1',
        'name' => 'MageAdmin Store View',
        'sort_order' => '0',
        'is_active' => '1',
      ),
      'hudsonreed_de_de' => 
      array (
        'store_id' => '2',
        'code' => 'hudsonreed_de_de',
        'website_id' => '2',
        'group_id' => '2',
        'name' => 'HudsonReed Germany DE',
        'sort_order' => '1',
        'is_active' => '1',
      ),
      'hudsonreed_it_it' => 
      array (
        'store_id' => '3',
        'code' => 'hudsonreed_it_it',
        'website_id' => '3',
        'group_id' => '3',
        'name' => 'HudsonReed Italy IT',
        'sort_order' => '1',
        'is_active' => '1',
      ),
      'hudsonreed_es_es' => 
      array (
        'store_id' => '4',
        'code' => 'hudsonreed_es_es',
        'website_id' => '4',
        'group_id' => '4',
        'name' => 'HudsonReed Spain ES',
        'sort_order' => '1',
        'is_active' => '1',
      ),
    ),
  ),
  /**
   * 'The configuration file doesn\'t contain the sensitive data by security reason. The sensitive data can be stored in the next environment variables:
   * CONFIG__DEFAULT__SYSTEM__MAGENTO_SCHEDULED_IMPORT_EXPORT_LOG__ERROR_EMAIL for system/magento_scheduled_import_export_log/error_email
   * CONFIG__DEFAULT__DEV__RESTRICT__ALLOW_IPS for dev/restrict/allow_ips
   * CONFIG__DEFAULT__PAYPAL__GENERAL__BUSINESS_ACCOUNT for paypal/general/business_account
   * CONFIG__DEFAULT__PAYPAL__WPP__API_USERNAME for paypal/wpp/api_username
   * CONFIG__DEFAULT__PAYPAL__WPP__API_PASSWORD for paypal/wpp/api_password
   * CONFIG__DEFAULT__PAYPAL__WPP__API_SIGNATURE for paypal/wpp/api_signature
   * CONFIG__DEFAULT__PAYPAL__FETCH_REPORTS__FTP_LOGIN for paypal/fetch_reports/ftp_login
   * CONFIG__DEFAULT__PAYPAL__FETCH_REPORTS__FTP_PASSWORD for paypal/fetch_reports/ftp_password
   * CONFIG__DEFAULT__PAYPAL__FETCH_REPORTS__FTP_IP for paypal/fetch_reports/ftp_ip
   * CONFIG__DEFAULT__PAYPAL__FETCH_REPORTS__FTP_PATH for paypal/fetch_reports/ftp_path
   * CONFIG__DEFAULT__PAYMENT__BRAINTREE__MERCHANT_ID for payment/braintree/merchant_id
   * CONFIG__DEFAULT__PAYMENT__BRAINTREE__PUBLIC_KEY for payment/braintree/public_key
   * CONFIG__DEFAULT__PAYMENT__BRAINTREE__PRIVATE_KEY for payment/braintree/private_key
   * CONFIG__DEFAULT__PAYMENT__BRAINTREE__MERCHANT_ACCOUNT_ID for payment/braintree/merchant_account_id
   * CONFIG__DEFAULT__PAYMENT__BRAINTREE_PAYPAL__MERCHANT_NAME_OVERRIDE for payment/braintree_paypal/merchant_name_override
   * CONFIG__DEFAULT__PAYMENT__PAYPAL_EXPRESS__MERCHANT_ID for payment/paypal_express/merchant_id
   * CONFIG__DEFAULT__PAYMENT__CYBERSOURCE__MERCHANT_ID for payment/cybersource/merchant_id
   * CONFIG__DEFAULT__PAYMENT__CYBERSOURCE__TRANSACTION_KEY for payment/cybersource/transaction_key
   * CONFIG__DEFAULT__PAYMENT__CYBERSOURCE__PROFILE_ID for payment/cybersource/profile_id
   * CONFIG__DEFAULT__PAYMENT__CYBERSOURCE__ACCESS_KEY for payment/cybersource/access_key
   * CONFIG__DEFAULT__PAYMENT__CYBERSOURCE__SECRET_KEY for payment/cybersource/secret_key
   * CONFIG__DEFAULT__PAYMENT__AUTHORIZENET_DIRECTPOST__LOGIN for payment/authorizenet_directpost/login
   * CONFIG__DEFAULT__PAYMENT__AUTHORIZENET_DIRECTPOST__TRANS_KEY for payment/authorizenet_directpost/trans_key
   * CONFIG__DEFAULT__PAYMENT__AUTHORIZENET_DIRECTPOST__TRANS_MD5 for payment/authorizenet_directpost/trans_md5
   * CONFIG__DEFAULT__PAYMENT__AUTHORIZENET_DIRECTPOST__MERCHANT_EMAIL for payment/authorizenet_directpost/merchant_email
   * CONFIG__DEFAULT__PAYMENT__WORLDPAY__INSTALLATION_ID for payment/worldpay/installation_id
   * CONFIG__DEFAULT__PAYMENT__WORLDPAY__RESPONSE_PASSWORD for payment/worldpay/response_password
   * CONFIG__DEFAULT__PAYMENT__WORLDPAY__ADMIN_INSTALLATION_ID for payment/worldpay/admin_installation_id
   * CONFIG__DEFAULT__PAYMENT__WORLDPAY__AUTH_PASSWORD for payment/worldpay/auth_password
   * CONFIG__DEFAULT__PAYMENT__WORLDPAY__MD5_SECRET for payment/worldpay/md5_secret
   * CONFIG__DEFAULT__PAYMENT__WORLDPAY__SIGNATURE_FIELDS for payment/worldpay/signature_fields
   * CONFIG__DEFAULT__PAYMENT__EWAY__LIVE_API_KEY for payment/eway/live_api_key
   * CONFIG__DEFAULT__PAYMENT__EWAY__LIVE_API_PASSWORD for payment/eway/live_api_password
   * CONFIG__DEFAULT__PAYMENT__EWAY__LIVE_ENCRYPTION_KEY for payment/eway/live_encryption_key
   * CONFIG__DEFAULT__PAYMENT__EWAY__SANDBOX_API_KEY for payment/eway/sandbox_api_key
   * CONFIG__DEFAULT__PAYMENT__EWAY__SANDBOX_API_PASSWORD for payment/eway/sandbox_api_password
   * CONFIG__DEFAULT__PAYMENT__EWAY__SANDBOX_ENCRYPTION_KEY for payment/eway/sandbox_encryption_key
   * CONFIG__DEFAULT__GOOGLE__ANALYTICS__CONTAINER_ID for google/analytics/container_id
   * CONFIG__DEFAULT__CHECKOUT__PAYMENT_FAILED__COPY_TO for checkout/payment_failed/copy_to
   * CONFIG__DEFAULT__SHIPPING__ORIGIN__COUNTRY_ID for shipping/origin/country_id
   * CONFIG__DEFAULT__SHIPPING__ORIGIN__REGION_ID for shipping/origin/region_id
   * CONFIG__DEFAULT__SHIPPING__ORIGIN__POSTCODE for shipping/origin/postcode
   * CONFIG__DEFAULT__SHIPPING__ORIGIN__CITY for shipping/origin/city
   * CONFIG__DEFAULT__SHIPPING__ORIGIN__STREET_LINE1 for shipping/origin/street_line1
   * CONFIG__DEFAULT__SHIPPING__ORIGIN__STREET_LINE2 for shipping/origin/street_line2'
   */
  'system' => 
  array (
    'default' => 
    array (
      'web' => 
      array (
        'seo' => 
        array (
          'use_rewrites' => '1',
        ),
        'unsecure' => 
        array (
          'base_url' => 'http://www.mageweb.co.uk/',
          'base_static_url' => NULL,
          'base_media_url' => NULL,
        ),
        'secure' => 
        array (
          'base_url' => 'https://www.mageweb.co.uk/',
          'base_static_url' => NULL,
          'base_media_url' => NULL,
          'enable_hsts' => '0',
          'enable_upgrade_insecure' => '0',
        ),
        'cookie' => 
        array (
          'cookie_path' => NULL,
          'cookie_domain' => NULL,
          'cookie_httponly' => '1',
        ),
      ),
      'general' => 
      array (
        'locale' => 
        array (
          'code' => 'en_GB',
          'timezone' => 'Europe/London',
        ),
        'region' => 
        array (
          'display_all' => '1',
          'state_required' => 'AT,BR,CA,CH,EE,ES,FI,LT,LV,RO,US',
        ),
      ),
      'currency' => 
      array (
        'options' => 
        array (
          'base' => 'GBP',
          'default' => 'GBP',
          'allow' => 'GBP',
        ),
      ),
      'catalog' => 
      array (
        'category' => 
        array (
          'root_id' => '2',
        ),
      ),
      'system' => 
      array (
        'mysqlmq' => 
        array (
          'successful_messages_lifetime' => '10080',
          'retry_inprogress_after' => '1440',
          'failed_messages_lifetime' => '10080',
          'new_messages_lifetime' => '10080',
        ),
        'smtp' => 
        array (
          'set_return_path' => '0',
        ),
        'backup' => 
        array (
          'enabled' => '0',
        ),
        'rotation' => 
        array (
          'lifetime' => '60',
          'frequency' => '1',
        ),
        'full_page_cache' => 
        array (
          'caching_application' => '2',
          'varnish' => 
          array (
            'access_list' => '10.4.0.0/16',
            'backend_host' => '10.4.31.83',
            'backend_port' => '80',
          ),
        ),
        'magento_scheduled_import_export_log' => 
        array (
          'save_days' => '5',
          'enabled' => '0',
          'time' => '00,00,00',
          'frequency' => 'D',
          'error_email_identity' => 'general',
          'error_email_template' => 'system_magento_scheduled_import_export_log_error_email_template',
        ),
      ),
      'crontab' => 
      array (
        'default' => 
        array (
          'jobs' => 
          array (
            'magento_scheduled_import_export_log_clean' => 
            array (
              'schedule' => 
              array (
                'cron_expr' => '0 0 * * *',
              ),
            ),
          ),
        ),
      ),
      'dev' => 
      array (
        'restrict' => 
        array (
        ),
        'debug' => 
        array (
          'template_hints_storefront' => '0',
          'template_hints_admin' => '0',
          'template_hints_blocks' => '0',
        ),
        'template' => 
        array (
          'allow_symlink' => '0',
        ),
        'translate_inline' => 
        array (
          'active' => '0',
          'active_admin' => '0',
        ),
        'js' => 
        array (
          'merge_files' => '1',
          'enable_js_bundling' => '1',
          'minify_files' => '1',
        ),
        'css' => 
        array (
          'merge_css_files' => '1',
          'minify_files' => '1',
        ),
        'static' => 
        array (
          'sign' => '0',
        ),
      ),
      'paypal' => 
      array (
        'general' => 
        array (
          'merchant_country' => 'GB',
        ),
        'wpp' => 
        array (
          'api_authentication' => '0',
          'sandbox_flag' => '1',
          'use_proxy' => '0',
          'button_flavor' => 'dynamic',
        ),
        'fetch_reports' => 
        array (
          'ftp_sandbox' => '0',
          'active' => '0',
          'schedule' => '1',
          'time' => '00,00,00',
        ),
        'style' => 
        array (
          'logo' => NULL,
          'page_style' => NULL,
          'paypal_hdrimg' => NULL,
          'paypal_hdrbackcolor' => NULL,
          'paypal_hdrbordercolor' => NULL,
          'paypal_payflowcolor' => NULL,
        ),
      ),
      'payment' => 
      array (
        'braintree' => 
        array (
          'active' => '0',
          'title' => 'Credit Card (Braintree)',
          'environment' => 'sandbox',
          'payment_action' => 'authorize',
          'fraudprotection' => '0',
          'debug' => '0',
          'useccv' => '1',
          'cctypes' => 'CUP,AE,VI,MC,DI,JCB,DN,MI',
          'sort_order' => NULL,
          'allowspecific' => '0',
          'specificcountry' => NULL,
          'countrycreditcard' => 'a:0:{}',
          'verify_3dsecure' => '0',
          'threshold_amount' => NULL,
          'verify_all_countries' => '0',
          'verify_specific_countries' => NULL,
        ),
        'braintree_paypal' => 
        array (
          'active' => '0',
          'title' => 'PayPal (Braintree)',
          'sort_order' => NULL,
          'payment_action' => 'authorize',
          'allowspecific' => '0',
          'specificcountry' => NULL,
          'require_billing_address' => '0',
          'allow_shipping_address_override' => '1',
          'debug' => '0',
          'display_on_shopping_cart' => '1',
        ),
        'braintree_cc_vault' => 
        array (
          'active' => '0',
          'title' => 'Stored Cards (Braintree)',
        ),
        'hosted_pro' => 
        array (
          'active' => '0',
          'title' => 'Payment by cards or by PayPal account',
          'sort_order' => NULL,
          'payment_action' => 'Authorization',
          'display_ec' => '0',
          'allowspecific' => '0',
          'debug' => '0',
          'verify_peer' => '1',
        ),
        'paypal_billing_agreement' => 
        array (
          'active' => '1',
          'title' => 'PayPal Billing Agreement',
          'sort_order' => NULL,
          'payment_action' => 'Authorization',
          'allowspecific' => '0',
          'debug' => '0',
          'verify_peer' => '1',
          'line_items_enabled' => '0',
          'allow_billing_agreement_wizard' => '1',
        ),
        'paypal_express' => 
        array (
          'title' => 'PayPal Express Checkout',
          'sort_order' => '1',
          'payment_action' => 'Sale',
          'visible_on_product' => '0',
          'visible_on_cart' => '1',
          'allowspecific' => '0',
          'debug' => '0',
          'verify_peer' => '0',
          'line_items_enabled' => '1',
          'transfer_shipping_options' => '0',
          'solution_type' => 'Mark',
          'require_billing_address' => '0',
          'allow_ba_signup' => 'never',
          'skip_order_review_step' => '0',
          'active' => '0',
          'in_context' => '0',
        ),
        'wps_express' => 
        array (
          'active' => '0',
        ),
        'checkmo' => 
        array (
          'specificcountry' => NULL,
          'payable_to' => NULL,
          'mailing_address' => NULL,
          'min_order_total' => NULL,
          'max_order_total' => NULL,
          'sort_order' => NULL,
          'active' => '0',
        ),
        'banktransfer' => 
        array (
          'active' => '1',
          'specificcountry' => NULL,
          'instructions' => NULL,
          'min_order_total' => NULL,
          'max_order_total' => NULL,
          'sort_order' => '99',
        ),
        'cashondelivery' => 
        array (
          'active' => '0',
          'specificcountry' => NULL,
          'instructions' => NULL,
          'min_order_total' => NULL,
          'max_order_total' => NULL,
          'sort_order' => NULL,
        ),
        'free' => 
        array (
          'specificcountry' => NULL,
        ),
        'purchaseorder' => 
        array (
          'specificcountry' => NULL,
          'min_order_total' => NULL,
          'max_order_total' => NULL,
          'sort_order' => NULL,
        ),
        'cybersource' => 
        array (
          'active' => '0',
          'payment_action' => 'authorize',
          'title' => 'Credit Card (Cybersource)',
          'order_status' => 'processing',
          'sandbox_flag' => '1',
          'debug' => '0',
          'cctypes' => 'AE,VI,MC,DI,JCB,DN,MI,MD',
          'allowspecific' => '0',
          'min_order_total' => NULL,
          'max_order_total' => NULL,
          'sort_order' => NULL,
        ),
        'authorizenet_directpost' => 
        array (
          'useccv' => '0',
          'min_order_total' => NULL,
          'max_order_total' => NULL,
          'sort_order' => NULL,
        ),
        'worldpay' => 
        array (
          'active' => '0',
          'title' => 'Payment method (Worldpay)',
          'fix_contact' => '1',
          'hide_contact' => '0',
          'debug' => '1',
          'sandbox_flag' => '1',
          'test_action' => 'AUTHORISED',
          'payment_action' => 'authorize',
          'allowspecific' => '0',
          'cvv_fraud_case' => NULL,
          'avs_fraud_case' => NULL,
          'sort_order' => '99',
        ),
        'eway' => 
        array (
          'active' => '0',
          'connection_type' => 'direct',
          'title' => 'Credit Card (eWAY)',
          'sandbox_flag' => '1',
          'payment_action' => 'authorize',
          'debug' => '1',
          'cctypes' => 'AE,VI,MC,JCB,DN',
          'allowspecific' => '0',
          'sort_order' => NULL,
        ),
        'worldpay_payments_card' => 
        array (
          'mode' => 'test_mode',
          'test_service_key' => 'T_S_a9b49af8-46f4-4a50-87ec-a8f9f65ec05f',
          'test_client_key' => 'T_C_77218eed-71b2-4d16-b450-e876d7cb5942',
          'live_service_key' => NULL,
          'live_client_key' => NULL,
          'settlement_currency' => 'GBP',
          'shop_country_code' => 'GB',
          'order_status' => 'processing',
          'payment_description' => NULL,
          'language_code' => 'EN',
          'sitecodes' => 'a:0:{}',
          'active' => '0',
          'title' => 'Credit / Debit Card',
          'payment_action' => 'authorize_capture',
          'save_card' => '1',
          'threeds_enabled' => '1',
          'debug' => '0',
        ),
        'worldpay_payments_paypal' => 
        array (
          'active' => '0',
          'title' => 'PayPal',
          'debug' => '0',
        ),
        'worldpay_payments_alipay' => 
        array (
          'active' => '0',
          'title' => 'Worldpay Alipay',
          'debug' => '0',
        ),
        'worldpay_payments_giropay' => 
        array (
          'active' => '0',
          'title' => 'Giropay',
          'debug' => '0',
        ),
        'worldpay_payments_ideal' => 
        array (
          'active' => '0',
          'title' => 'Worldpay iDeal',
          'debug' => '0',
        ),
        'worldpay_payments_mistercash' => 
        array (
          'active' => '0',
          'title' => 'Worldpay Mistercash',
          'debug' => '0',
        ),
        'worldpay_payments_przelewy24' => 
        array (
          'active' => '0',
          'title' => 'Worldpay Przelewy24',
          'debug' => '0',
        ),
        'worldpay_payments_paysafecard' => 
        array (
          'active' => '0',
          'title' => 'Worldpay PaySafeCard',
          'debug' => '0',
        ),
        'worldpay_payments_postepay' => 
        array (
          'active' => '0',
          'title' => 'Worldpay Postepay',
          'debug' => '0',
        ),
        'worldpay_payments_qiwi' => 
        array (
          'active' => '0',
          'title' => 'Worldpay Qiwi',
          'debug' => '0',
        ),
        'worldpay_payments_sofort' => 
        array (
          'active' => '0',
          'title' => 'Worldpay Sofort',
          'debug' => '0',
        ),
        'worldpay_payments_yandex' => 
        array (
          'active' => '0',
          'title' => 'Worldpay Yandex',
          'debug' => '0',
        ),
      ),
      'admin' => 
      array (
        'security' => 
        array (
          'use_case_sensitive_login' => '0',
          'session_lifetime' => '31536000',
        ),
        'magento_logging' => 
        array (
          'actions' => 'a:61:{s:24:"adminhtml_system_account";s:1:"1";s:26:"adminhtml_permission_roles";s:1:"1";s:26:"adminhtml_permission_users";s:1:"1";s:11:"admin_login";s:1:"1";s:10:"cms_blocks";s:1:"1";s:29:"magento_versionscms_hierarchy";s:1:"1";s:17:"version_cms_pages";s:1:"1";s:9:"cms_pages";s:1:"1";s:16:"cache_management";s:1:"1";s:9:"salesrule";s:1:"1";s:18:"catalog_attributes";s:1:"1";s:18:"catalog_categories";s:1:"1";s:20:"magento_catalogevent";s:1:"1";s:11:"catalogrule";s:1:"1";s:23:"tax_product_tax_classes";s:1:"1";s:21:"catalog_attributesets";s:1:"1";s:16:"catalog_products";s:1:"1";s:6:"rating";s:1:"1";s:6:"review";s:1:"1";s:13:"catalogsearch";s:1:"1";s:15:"sales_agreement";s:1:"1";s:25:"adminhtml_system_variable";s:1:"1";s:15:"customer_groups";s:1:"1";s:18:"magento_invitation";s:1:"1";s:24:"tax_customer_tax_classes";s:1:"1";s:8:"customer";s:1:"1";s:23:"magento_giftcardaccount";s:1:"1";s:27:"magento_giftregistry_entity";s:1:"1";s:25:"magento_giftregistry_type";s:1:"1";s:14:"magento_banner";s:1:"1";s:25:"adminhtml_system_currency";s:1:"1";s:36:"adminhtml_customer_address_attribute";s:1:"1";s:28:"adminhtml_customer_attribute";s:1:"1";s:23:"adminhtml_system_design";s:1:"1";s:23:"magento_customersegment";s:1:"1";s:23:"adminhtml_system_stores";s:1:"1";s:29:"adminhtml_system_store_groups";s:1:"1";s:25:"adminhtml_system_websites";s:1:"1";s:16:"newsletter_queue";s:1:"1";s:22:"newsletter_subscribers";s:1:"1";s:20:"newsletter_templates";s:1:"1";s:25:"paypal_settlement_reports";s:1:"1";s:7:"reports";s:1:"1";s:19:"magento_reward_rate";s:1:"1";s:18:"magento_targetrule";s:1:"1";s:20:"magento_salesarchive";s:1:"1";s:17:"sales_creditmemos";s:1:"1";s:14:"sales_invoices";s:1:"1";s:18:"sales_order_status";s:1:"1";s:12:"sales_orders";s:1:"1";s:15:"sales_shipments";s:1:"1";s:24:"magento_advancedcheckout";s:1:"1";s:23:"magento_customerbalance";s:1:"1";s:7:"backups";s:1:"1";s:23:"adminhtml_system_config";s:1:"1";s:9:"tax_rates";s:1:"1";s:9:"tax_rules";s:1:"1";s:24:"adminhtml_email_template";s:1:"1";s:11:"urlrewrites";s:1:"1";s:15:"widget_instance";s:1:"1";s:14:"google_sitemap";s:1:"1";}',
        ),
      ),
      'google' => 
      array (
        'analytics' => 
        array (
          'active' => '1',
          'type' => 'tag_manager',
          'experiments' => '0',
          'catalog_page_list_value' => 'Catalog Page',
          'crosssell_block_list_value' => 'Cross-sell',
          'upsell_block_list_value' => 'Up-sell',
          'related_block_list_value' => 'Related Products',
          'search_page_list_value' => 'Search Results',
          'promotions_list_value' => 'Label',
        ),
      ),
      'multishipping' => 
      array (
        'options' => 
        array (
          'checkout_multiple' => '0',
        ),
      ),
      'magento_giftregistry' => 
      array (
        'general' => 
        array (
          'enabled' => '0',
          'max_registrant' => '5',
        ),
        'owner_email' => 
        array (
          'template' => 'magento_giftregistry_owner_email_template',
          'identity' => 'general',
        ),
        'sharing_email' => 
        array (
          'template' => 'magento_giftregistry_sharing_email_template',
          'identity' => 'general',
          'send_limit' => '3',
        ),
        'update_email' => 
        array (
          'template' => 'magento_giftregistry_update_email_template',
          'identity' => 'general',
        ),
      ),
      'giftcard' => 
      array (
        'email' => 
        array (
          'identity' => 'general',
          'template' => 'giftcard_email_template',
        ),
        'general' => 
        array (
          'is_redeemable' => '1',
          'lifetime' => '0',
          'allow_message' => '0',
          'message_max_length' => '255',
          'order_item_status' => '9',
        ),
        'giftcardaccount_email' => 
        array (
          'identity' => 'general',
          'template' => 'giftcard_giftcardaccount_email_template',
        ),
        'giftcardaccount_general' => 
        array (
          'code_length' => '12',
          'code_format' => 'alphanum',
          'code_prefix' => NULL,
          'code_suffix' => NULL,
          'code_split' => '0',
          'pool_size' => '1000',
          'pool_threshold' => '100',
        ),
      ),
      'sales' => 
      array (
        'general' => 
        array (
          'hide_customer_ip' => '0',
        ),
        'totals_sort' => 
        array (
          'giftcardaccount' => '90',
          'customerbalance' => '95',
        ),
        'identity' => 
        array (
          'address' => NULL,
          'logo' => NULL,
          'logo_html' => NULL,
        ),
        'minimum_order' => 
        array (
          'active' => '0',
          'amount' => NULL,
          'description' => NULL,
          'error_message' => NULL,
          'multi_address' => '0',
          'multi_address_description' => NULL,
          'multi_address_error_message' => NULL,
        ),
        'gift_options' => 
        array (
          'wrapping_allow_order' => '0',
          'wrapping_allow_items' => '0',
          'allow_gift_receipt' => '0',
          'allow_printed_card' => '0',
          'printed_card_price' => NULL,
        ),
        'product_sku' => 
        array (
          'my_account_enable' => '1',
        ),
        'magento_salesarchive' => 
        array (
          'active' => '0',
          'age' => '30',
          'order_statuses' => 'complete,closed',
        ),
        'magento_rma' => 
        array (
          'enabled' => '0',
          'enabled_on_product' => '1',
          'use_store_address' => '1',
        ),
      ),
      'customer' => 
      array (
        'online_customers' => 
        array (
          'online_minutes_interval' => NULL,
        ),
        'create_account' => 
        array (
          'auto_group_assign' => '0',
          'viv_disable_auto_group_assign_default' => '0',
          'generate_human_friendly_id' => '0',
        ),
        'password' => 
        array (
          'remind_email_template' => '11',
        ),
        'address' => 
        array (
          'prefix_options' => NULL,
          'suffix_options' => NULL,
        ),
        'magento_customerbalance' => 
        array (
          'is_enabled' => '1',
          'show_history' => '1',
          'refund_automatically' => '0',
          'email_identity' => 'general',
          'email_template' => 'customer_magento_customerbalance_email_template',
        ),
        'magento_customersegment' => 
        array (
          'is_enabled' => '1',
        ),
      ),
      'checkout' => 
      array (
        'options' => 
        array (
          'guest_checkout' => '1',
          'enable_agreements' => '0',
        ),
        'cart' => 
        array (
          'preview_quota_lifetime' => '30',
        ),
        'payment_failed' => 
        array (
          'copy_method' => 'bcc',
        ),
      ),
      'cataloginventory' => 
      array (
        'item_options' => 
        array (
          'use_deferred_stock_update' => '0',
          'auto_return' => '0',
          'notify_stock_qty' => '1',
        ),
        'options' => 
        array (
          'show_out_of_stock' => '1',
        ),
      ),
      'tax' => 
      array (
        'notification' => 
        array (
          'ignore_discount' => '0',
          'ignore_price_display' => '1',
        ),
        'classes' => 
        array (
          'wrapping_tax_class' => '0',
        ),
        'calculation' => 
        array (
          'cross_border_trade_enabled' => '0',
          'price_includes_tax' => '1',
        ),
        'defaults' => 
        array (
          'postcode' => '*',
          'country' => 'GB',
          'region' => '0',
        ),
        'cart_display' => 
        array (
          'gift_wrapping' => '3',
          'printed_card' => '3',
          'price' => '3',
          'subtotal' => '3',
          'shipping' => '3',
        ),
        'sales_display' => 
        array (
          'gift_wrapping' => '1',
          'printed_card' => '1',
        ),
        'display' => 
        array (
          'type' => '2',
          'shipping' => '2',
        ),
      ),
      'shipping' => 
      array (
        'origin' => 
        array (
        ),
        'shipping_policy' => 
        array (
          'enable_shipping_policy' => '0',
          'shipping_policy_content' => NULL,
        ),
      ),
    ),
    'websites' => 
    array (
      'hudsonreed_de' => 
      array (
        'web' => 
        array (
          'unsecure' => 
          array (
            'base_url' => 'http://hudsonreed-de.mageweb.co.uk/',
          ),
          'default' => 
          array (
            'cms_home_page' => 'home|8',
          ),
          'secure' => 
          array (
            'base_url' => 'http://hudsonreed-de.mageweb.co.uk/',
          ),
          'cookie' => 
          array (
            'cookie_domain' => 'hudsonreed-de.mageweb.co.uk',
          ),
        ),
        'magento_giftregistry' => 
        array (
          'general' => 
          array (
            'enabled' => '0',
          ),
        ),
        'payment' => 
        array (
          'worldpay_payments_card' => 
          array (
            'settlement_currency' => 'EUR',
            'shop_country_code' => 'DE',
            'order_status' => 'processing',
            'payment_description' => 'Complete',
            'language_code' => 'DE',
            'sitecodes' => 'a:1:{s:17:"_1480073069075_75";a:3:{s:8:"currency";s:3:"EUR";s:19:"settlement_currency";s:3:"EUR";s:9:"site_code";s:12:"HUDSONREEDDE";}}',
            'active' => '1',
          ),
          'worldpay_payments_giropay' => 
          array (
            'active' => '1',
          ),
        ),
        'tax' => 
        array (
          'defaults' => 
          array (
            'country' => 'DE',
          ),
        ),
      ),
      'mageadmin' => 
      array (
        'web' => 
        array (
          'cookie' => 
          array (
            'cookie_domain' => 'www.mageweb.co.uk',
          ),
        ),
        'payment' => 
        array (
          'worldpay_payments_card' => 
          array (
            'sitecodes' => 'a:1:{s:18:"_1479295214636_636";a:3:{s:8:"currency";s:3:"GBP";s:19:"settlement_currency";s:3:"GBP";s:9:"site_code";s:12:"HUDSONREEDDE";}}',
          ),
          'worldpay' => 
          array (
            'active' => '0',
          ),
          'paypal_express' => 
          array (
            'sort_order' => '1',
            'visible_on_product' => '0',
            'active' => '1',
          ),
        ),
        'design' => 
        array (
          'head' => 
          array (
            'title_prefix' => NULL,
            'title_suffix' => NULL,
            'includes' => NULL,
          ),
          'header' => 
          array (
            'logo_width' => NULL,
            'logo_height' => NULL,
          ),
          'footer' => 
          array (
            'absolute_footer' => NULL,
          ),
          'pagination' => 
          array (
            'pagination_frame_skip' => NULL,
            'anchor_text_for_previous' => NULL,
            'anchor_text_for_next' => NULL,
          ),
          'watermark' => 
          array (
            'image_size' => NULL,
            'image_imageOpacity' => NULL,
            'small_image_size' => NULL,
            'small_image_imageOpacity' => NULL,
            'thumbnail_size' => NULL,
            'thumbnail_imageOpacity' => NULL,
            'swatch_image_size' => NULL,
            'swatch_image_imageOpacity' => NULL,
          ),
          'email' => 
          array (
            'logo' => 'websites/1/LDG_Logo.JPG',
            'logo_alt' => NULL,
            'logo_width' => NULL,
            'logo_height' => NULL,
          ),
        ),
      ),
      'hudsonreed_it' => 
      array (
        'shipping' => 
        array (
          'origin' => 
          array (
          ),
        ),
      ),
    ),
    'stores' => 
    array (
      'hudsonreed_de_de' => 
      array (
        'design' => 
        array (
          'head' => 
          array (
            'title_prefix' => NULL,
            'title_suffix' => NULL,
            'includes' => NULL,
          ),
          'header' => 
          array (
            'logo_width' => '215',
            'logo_height' => NULL,
            'logo_src' => 'stores/2/hr-eu.svg',
          ),
          'footer' => 
          array (
            'absolute_footer' => NULL,
            'copyright' => '© HudsonReed DE 2016',
          ),
          'pagination' => 
          array (
            'pagination_frame_skip' => NULL,
            'anchor_text_for_previous' => NULL,
            'anchor_text_for_next' => NULL,
          ),
          'watermark' => 
          array (
            'image_size' => NULL,
            'image_imageOpacity' => NULL,
            'small_image_size' => NULL,
            'small_image_imageOpacity' => NULL,
            'thumbnail_size' => NULL,
            'thumbnail_imageOpacity' => NULL,
            'swatch_image_size' => NULL,
            'swatch_image_imageOpacity' => NULL,
          ),
          'email' => 
          array (
            'logo_alt' => NULL,
            'logo_width' => '215',
            'logo_height' => '64',
            'logo' => 'stores/2/hr-eu.svg',
            'header_template' => '5',
            'footer_template' => '31',
          ),
          'theme' => 
          array (
            'theme_id' => '6',
          ),
        ),
        'magento_giftregistry' => 
        array (
          'general' => 
          array (
            'enabled' => '0',
          ),
        ),
        'general' => 
        array (
          'store_information' => 
          array (
            'name' => 'Hudsonreed Germany',
            'phone' => '6666666666',
            'hours' => '9am - 5pm',
          ),
        ),
      ),
    ),
  ),
  'i18n' => 
  array (
  )
);
