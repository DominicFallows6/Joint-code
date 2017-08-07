<?php

include('Tasks.php');
$TasksClass = new Tasks();
$actionType = 'add';
$id = isset ($_GET['id']);
if (isset ($_POST['action'])) {

    $postData = $_POST;
    $action = $_POST['action'];
    $id = $_POST['hiddenid'];

    if ($_POST['action'] === 'edit') {

        $TasksClass->edit($action, $postData, $id);

    } elseif ($_POST['action'] === 'add') {


        $TasksClass->edit($action, $postData);

    }


    header('Location: index.php');
    exit;
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
        $actionType = 'edit';

    }
}




require 'add.view.php';