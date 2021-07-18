<?php
//Classes used in this file
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/User.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/Article.php";

/**
 * Class address_controller
 */
class address_controller
{
    /**
     * Generates a new address which is assigned to the given user in the database
     *
     * @param User $user The user to whom the address is assigned
     * @param Address $address The address assigned to the user
     * @return The address as object
     * @throws Exception Exception is thrown if the connection to the database fails or the SQL-Statement is incorrect
     */
    function add_address(User $user, $address): Address
    {
        try {
            global $connection;

            $sql = "INSERT INTO marketplace_db.Address (str_street, str_number, str_zip, str_city, fk_user) VALUES (:street, :number, :zip, :city, :user_id);";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":street", $address["street"]);
            $stmt->bindparam(":number", $address["number"]);
            $stmt->bindparam(":zip", $address["zip"]);
            $stmt->bindparam(":city", $address["city"]);
            $stmt->bindparam(":user_id", $user->id);
            $stmt->execute();

            $sql = "SELECT id FROM marketplace_db.Address WHERE fk_user = :id ORDER BY id DESC LIMIT 1;";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":id", $user->id);
            $stmt->execute();
            $id = $stmt->fetch()["id"];

            return new Address($id);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Assigns a primary address to the user
     *
     * @param $user The user who sets his primary address
     * @param $address The address which is going to be the primary address
     * @return bool State if the SQL-Statement worked
     * @throws Exception Exception is thrown if the connection to the database fails or the SQL-Statement is incorrect
     */
    function set_primary($user, $address): bool
    {
        try {
            global $connection;

            $sql = "UPDATE marketplace_db.Address SET bool_primary = 0 WHERE fk_user = :user_id;";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":user_id", $user->id);
            $stmt->execute();

            $sql = "UPDATE marketplace_db.Address SET bool_primary = 1 WHERE id = :id;";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":id", $address);
            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

}