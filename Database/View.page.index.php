<?php

require 'bootstrap.php';

$actionType = 'add';

if (isset ($_GET['option'])) {

    $id = $_GET['id'];


    if ($_GET['option'] == 'view') {

        $ViewData = 'SELECT * FROM todos WHERE id=' . $id;
//$selecteddata=$pdo->exec($selectdata);
        $stmt = $pdo->query($ViewData);
        $row = $stmt->fetchObject();
        $DescriptionText = $row->description;
        $NameText = $row->Name;
        $DateText = $row->Date;
        $actionType = 'edit';
//the option is changing off of edit when we click the submit button

    }

    elseif ($_GET['option'] == 'edit') {

//        $selectdata= 'SELECT description FROM todos WHERE id='.$id;
//        //$selecteddata=$pdo->exec($selectdata);
//        $stmt = $pdo->query($selectdata);
//        $row = $stmt->fetchObject();
//        $selectedit=$row->description;
//        $edit = $selectedit;


        $EditData = 'SELECT * FROM todos WHERE id=' . $id;
        $stmt = $pdo->query($EditData);
        $row = $stmt->fetchObject();
        $DescriptionText = $row->description;
        $NameText = $row->Name;


    }

    elseif ($_POST['action'] === 'add') {
        $desc = $_POST['EditInput'];
        $insert = $pdo->prepare("INSERT INTO todos (description) VALUES (?)");
        $insert->execute([$desc]);
    }
}



