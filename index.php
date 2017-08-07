<?php

require 'bootstrap.php';
require 'Task.php';


$query = new QueryBuilder($pdo);

$tasks = $query->selectALL('todos');

$edit="";


if (isset ($_POST['action'])) {
    if ($_POST['action'] === 'edit') {
        $id=$_GET['id'];
        $NewDesc=$_POST['EditInput'];
        $updating = "UPDATE todos SET description='$NewDesc' WHERE id = $id LIMIT 1";
        $pdo->exec($updating);
    } elseif ($_POST['action'] === 'add') {
        $desc = $_POST['EditInput'];
        $insert = $pdo->prepare("INSERT INTO todos (description, completed) VALUES (?,?)");
        $insert->execute([$desc, 0]);
    }

    header('Location: index.php');
    exit;
}

$actionType = 'add';

if (isset ($_GET['option'])) {

    $id=$_GET['id'];

    if ($_GET['option'] == 'delete') {

        $deletesql = 'DELETE FROM todos WHERE id='.$id;
        $pdo->exec($deletesql);
        header('Location: index.php');
        exit;

    }

}

require 'index.view.php';