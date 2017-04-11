<?php

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\App\ResourceConnection;

/* set csv perameters */
const CSV = 'ORDER_IMPORT.csv';

/* load bootstrap */
use Magento\Framework\App\Bootstrap;
require __DIR__ . '../../../../bootstrap.php';
$bootstrap = Bootstrap::create(BP, $_SERVER);



/* create bootstrap object */
$objectManager = $bootstrap->getObjectManager();

$servername = "localhost";
$username = "root";
$password = "d4w50nc0urt75";
$database = 'magento2';

$conn = mysqli_connect($servername, $username, $password, $database);
if(!$conn) {
    throw new Exception("Problem connecting to mysql database: ".mysqli_connect_error());
}


$conn->set_charset("utf8");

if(file_exists(CSV)) {
    $handle = fopen(CSV, 'r');

    while ($data = fgetcsv($handle, 1000, ',')) {

        $legacyOrderId = $data[1];
        $userEmail = $data[5];
        $siteId = $data[4];
        $currencyType = $data[14];
        $shippingMethod = $data[15];
        $orderPhase = $data[2];
        $orderDate = $data[6];
        $orderTotal = str_replace('&#44;', ',', $data[7]);
        $orderSubtotal = str_replace('&#44;', ',', $data[8]);
        $deliveryValue = str_replace('&#44;', ',', $data[9]);
        $orderDiscount = str_replace('&#44;', ',',$data[12]);
        $paymentType = $data[16];
        $orderItems = str_replace('&#44;', ',', $data[13]);
        $deliveryAddress = str_replace('&#44;', ',', $data[10]);
        $billingAddress = str_replace('&#44;', ',', $data[11]);
        $vatValue = $data[17];


        if ($legacyOrderId != '' && count($data) == 17) {


            $importLegacyOrder = "insert into limitless_legacy_orders (legacy_order_id,user_email,site_id,currency_type,shipping_method,order_phase,order_date,order_total,order_subtotal,delivery_value,order_discount,payment_type,order_items,delivery_address,billing_address,vat_value) values ('" . $legacyOrderId . "','" . $userEmail . "','" . $siteId . "','" . $currencyType . "','" . $shippingMethod . "','" . $orderPhase . "','" . $orderDate . "','" . $orderTotal . "','" . $orderSubtotal . "','" . $deliveryValue . "','" . $orderDiscount . "','" . $paymentType . "','" . $orderItems . "','" . $deliveryAddress . "','" . $billingAddress . "','" . $vatValue . "'); ";
            echo $importLegacyOrder;

            mysqli_query($conn, $importLegacyOrder);

            
        }


    }
} else {
    __('file does not exist');
    exit;
}

?>