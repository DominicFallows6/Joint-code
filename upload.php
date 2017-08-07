<?php

require 'bootstrap.php';


if(isset($_POST["Submit"])){

    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);

    if ($check!== false) {
        echo "File is an image - " . $check["mime"] . ".";
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    if ($_FILES["fileToUpload"]["size"] > 8000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    if ($uploadOk) {

        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)){

            $id = $_POST['imagehiddenid'];
            $updating = "UPDATE todos SET image_name ='".basename($_FILES["fileToUpload"]["name"])."' WHERE id='$id'";
            $pdo->exec($updating);

            //redirect
            header('location: index.php?upload_success=1');

        } else {

            header('location: index.php?upload_success=0');
        }

        exit;
    }

}

$id = $_POST['imagehiddenid'];

require "upload.image.view.php";
