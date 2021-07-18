<?php
include($_SERVER['DOCUMENT_ROOT'] . "/database/connection.php");
if ($connection == null) {
    establish_db_connection();
}
$sql = "UPDATE marketplace_db.User t SET t.bool_active = 1 WHERE t.id = :id;";
$stmt = $connection->prepare($sql);
$stmt->bindparam(':id', $_REQUEST["id"]);
$stmt->execute();


header("Location: /pages/login.php");
