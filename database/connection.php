<?php
/*global variable*/
global $connection;

function establish_db_connection()
{
    //Variables for database connection
    $server = "";
    $user = "";
    $password = "";
    $database = "marketplace_db";
    $charset = "utf8";

    // data source name
    $dsn = "mysql:host=$server;dbname=$database;charset=$charset";

    global $connection; //use locally

    try {
        $connection = new PDO($dsn, $user, $password);

        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        throw new PDOException($e->getMessage());
    }

}

function close_db_connection()
{
    global $connection;

    $connection = null;
}