<?php

require 'bootstrap.php';

class Tasks
{
    public function edit($actionType, $dataInput, $id = null)
    {

        global $pdo;

        if ($actionType === 'edit') {
            $NewDesc=$dataInput['DescTexts'];
            $NewName=$dataInput['NameTexts'];
            $myDate = date('Y/m/d');
            $updating = "UPDATE todos SET description='$NewDesc', Name='$NewName', Date='$myDate' WHERE id='$id'";
            $pdo->exec($updating);
        } elseif ($actionType === 'add') {
            $imagename=$_FILES["myimage"]["name"];
            $Desc=$dataInput['DescTexts'];
            $Name=$dataInput['NameTexts'];
            $myDate = date('Y-m-d');
            $insert = $pdo->prepare("INSERT INTO todos (Name, description, Date) VALUES (?,?,?)");
            $insert->execute([$Name,$Desc,$myDate]);
        }

    }



}
