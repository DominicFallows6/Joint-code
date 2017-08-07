<?php

class Connection
{
    public static function make()
    {
        try {
            return new PDO('mysql:host=127.0.0.1;dbname=josh', 'root', 'Welcome17');
        } catch (PDOException $e) {

            die($e->getMessage());
        }
    }

}
