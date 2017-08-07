<?php

include('Tasks.php');
$TasksClass = new Tasks();
$actionType = 'add';
$id = isset ($_GET['id']);

if (isset ($_POST['action'])) {

    $postData = $_POST;
    $action = $_POST['action'];
    $id = $_POST['hiddenid'];

    $errors = [];

    if (isset($_POST['NameTexts']) && empty(trim($_POST['NameTexts']))) {
       $errors[] = 'Name Must Be Completed';
    }

    if (isset($_POST['DescTexts']) && empty(trim($_POST['DescTexts']))) {
       $errors[] = 'Description Must Be Completed';
    }

    if (empty($errors)) {

        if ($_POST['action'] === 'edit') {
            $TasksClass->edit($action, $postData, $id);
        } elseif ($_POST['action'] === 'add') {
            $TasksClass->edit($action, $postData);
        }

        header('Location: index.php');
        exit;

    } else {
        $DescriptionText = $_POST['DescTexts'];
        $NameText = $_POST['NameTexts'];
    }


}


if (isset ($_GET['option'])) {

    if ($_GET['option'] == 'add') {
        $NameText = $DescriptionText = '';
    } elseif ($_GET['option'] == 'edit') {
        $id = $_GET['id'];
        $ViewData = 'SELECT * FROM todos WHERE id=' . $_GET['id'];;
//$selecteddata=$pdo->exec($selectdata);
        $stmt = $pdo->query($ViewData);
        $row = $stmt->fetchObject();
        $DescriptionText = $row->description;
        $NameText = $row->Name;
        $DateText = $row->Date;
        $ImageNameText = $row->image_name;
        $actionType = 'edit';

    }
}




require 'add.view.php';