<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/database/connection.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/model/Address.php");

if (!isset($connection)) {
    establish_db_connection();
}


class Address
{
    /**
     * Attributes of an address
     */
    public $street;
    public $number;
    public $zip;
    public $city;
    private $id;
    private $primary;

    /**
     * Address constructor.
     * @param $id
     */
    function __construct($id)
    {
        $this->get_by_id($id);
    }

    /**
     * Get the data for an address object from the database
     *
     * @param int $id Id of an article registered in the database
     * @return $this A new Address object
     */
    public function get_by_id(int $id): Address
    {
        try {
            global $connection;

            $sql = "SELECT * from Address where id = :id;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();

            $sql_result = $stmt->fetch();

            $this->id = $id;
            $this->street = $sql_result["str_street"];
            $this->number = $sql_result["str_number"];
            $this->zip = $sql_result["str_zip"];
            $this->city = $sql_result["str_city"];
            $this->primary = $sql_result["bool_primary"];

            return $this;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Return a boolean which describes if the address is the primary address
     *
     * @return mixed Boolean representing the primary state
     */
    function get_primary()
    {
        return $this->primary;
    }

    /**
     * Returns id from address
     *
     * @return mixed Id
     */
    function get_id()
    {
        return $this->id;
    }

}