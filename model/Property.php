<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/database/connection.php");

if (!isset($connection)) {
    establish_db_connection();
}

class Property
{
    function __construct($id)
    {
        $this->get_by_id($id);
    }

    private $id;
    private $name = "";
    private $type;

    /**
     * Load object from database
     * @param $id . Id of object to load.
     * @return $this Property object.
     */
    public function get_by_id($id): Property
    {
        try {
            if ($id == "price") {
                $this->id = "price";
                $this->name = "price";
                $this->type = "float";
                return $this;
            } else {
                global $connection;

                $sql = "SELECT * from Property where id = :id;";

                $stmt = $connection->prepare($sql);
                $stmt->bindparam(':id', $id);
                $stmt->execute();
                $sql_result = $stmt->fetch();


                $this->id = $id;
                $this->name = $sql_result["str_name"];
                $this->type = $sql_result["str_type"];

                return $this;
            }
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /** Gets the Value of an Article to $this Property
     *
     * @param $id. of article to which the value is beeing requested
     * @return string containing value of article to $this property
     */
    public function get_value_by_article($id): string
    {

        try {
            global $connection;
            if ($this->id == "price") {
                $sql = "SELECT float_current_price from Article where id = :id;";

                $stmt = $connection->prepare($sql);
                $stmt->bindparam(':id', $id);
                $stmt->execute();
                $sql_result = $stmt->fetch();

                $value = $sql_result["float_current_price"];
                return $value;
            } else {
                $sql = "SELECT * from Article_Property where fk_property = :pid and fk_article = :id;";

                $stmt = $connection->prepare($sql);
                $stmt->bindparam(':pid', $this->id);
                $stmt->bindparam(':id', $id);
                $stmt->execute();
                $sql_result = $stmt->fetch();

                $value = $sql_result["str_value"];
                if (!isset($value)) return "";
                return $value;
            }
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * @return mixed
     */
    public
    function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
