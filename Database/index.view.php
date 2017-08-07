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
        <a href="View.page.php?id=<?= $todo->id ?>&option=view">View</a>

        </p>
<?php } ?>

</body>

</html>
