<?php

/* set csv perameters */
const CSV = 'CATEGORY_IMPORT.csv';

$servername = "enter_servername";
$username = "enter_username";
$password = "enter_password";
$database = 'magento2';

$conn = mysqli_connect($servername, $username, $password, $database);
if(!$conn) {
    throw new Exception("Problem connecting to mysql database: ".mysqli_connect_error());
}

$conn->set_charset("utf8");

if(file_exists(CSV)) {
    $handle = fopen(CSV, 'r');

    while ($data = fgetcsv($handle, 1000, ',')) {

        $categoryId = $data[0];
        $categoryDescription = $data[1];
        $categoryHeading = $data[2];
        $filterAttributeIds = $data[3];
        $staticBlock = $data[4];
        $status = $data[5];
        $storeId = $data[6];
        $metaDesc = $data[7];

        $attributes = explode('|', $filterAttributeIds);
        sort ($attributes);
        $attributes = implode('|', $attributes);

        if ($categoryId != '') {
            $importCustomCategory = "insert into limitless_custom_category (category_id,category_description,category_heading,filter_attribute_ids,static_block,status,store_id,meta_description) values ('" . $categoryId . "','" . $categoryDescription . "','" . $categoryHeading . "','" . $attributes . "','" . $staticBlock . "','" . $status . "','" . $storeId . "','" . $metaDesc . "'); ";
            mysqli_real_escape_string($conn,$importCustomCategory);
            mysqli_query($conn, $importCustomCategory);
            echo $importCustomCategory;
        }

    }
}

?>