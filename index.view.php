<html>
<title>Document</title>


<style>

    h1{

        text-align: center;
    }

    body{

        text-align: center;
    }

    #add1{

        font-size: x-large;
    }

    #list1{

        font-size: x-large;
    }

</style>

<head>
    <h1>Task for the day! </h1>
    <br><a id="add1" href="add.php?option=add">Add</a
</head>


<body>

<?php foreach ($tasks as $todo) { ?>


    <p id="list1">

        <?= $todo->Name;?><br>

        <a href="add.php?id=<?= $todo->id ?>&option=edit">Edit</a> ::
        <a href="index.php?id=<?= $todo->id ?>&option=delete">Delete</a> ::
        <a href="View.page.php?id=<?= $todo->id ?>&option=view">View</a> ::
        <a href="upload.php?id=<?= $todo->id ?>&option=addimage">Add Images</a>

    </p>



<?php } ?>

<?php
if (isset($_GET['upload_success'])) {
    if ($_GET['upload_success'] == '1') {
        echo "<script>alert('Success')</script>";
    } elseif ($_GET['upload_success'] == '0') {
        echo "<script>alert('Not a success')</script>";
    }
}
?>

</body>

</html>
