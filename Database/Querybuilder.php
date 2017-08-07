<?php

class Querybuilder
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function selectAll($table){

        $statement = $this->pdo->prepare('select * from todos');

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS);
    }

}