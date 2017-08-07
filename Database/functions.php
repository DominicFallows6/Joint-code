<?php


function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die('Done');
}





function fetchAllTasks($pdo)
{

    $statement = $pdo->prepare('select * from todos');

    $statement->execute();

    $todos = $statement->fetchall(PDO::FETCH_OBJ);

}
