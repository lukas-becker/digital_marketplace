<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/database/connection.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/model/Address.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/model/Article.php");

if (!isset($connection)) {
    establish_db_connection();
}

class Organization
{
    /**
     * Attributes of an organization
     */
    public $id;
    public $name;
    public $description;
    public $image;
    public $street;
    public $nr;
    public $zip;
    public $city;

    /**
     * Organization constructor.
     * @param $id
     */
    function __construct($id)
    {
        $this->get_by_id($id);
    }

    /**
     * Get the data for an organization object from the database
     *
     * @param int $id
     */
    private function get_by_id(int $id): void
    {
        try {
            global $connection;
            $sql = "SELECT id, str_name, str_description, str_organization_picture, str_street, str_nr, str_zip, str_city FROM `Organization` where id = :id;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();

            $sql_result = $stmt->fetch();

            $this->id = $sql_result['id'];
            $this->name = $sql_result['str_name'];
            $this->description = $sql_result['str_description'];
            $this->image = $sql_result['str_organization_picture'];
            $this->street = $sql_result['str_street'];
            $this->nr = $sql_result['str_nr'];
            $this->zip = $sql_result['str_zip'];
            $this->city = $sql_result['str_city'];


            return;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Reassigns the attributes to the object
     *
     * @throws Exception If id is not valid
     */
    public function refresh_from_db()
    {
        if ($this->id == null) {
            throw new Exception("You need to initialize the Organization Object");
        }

        $this->get_by_id($this->id);
    }
}