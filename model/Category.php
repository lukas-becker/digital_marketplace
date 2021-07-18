<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/database/connection.php");

if (!isset($connection)) {
    establish_db_connection();
}

class Category
{
    /**
     * Attributes of a category
     */
    private $id;
    public $name;
    public $properties;
    public $description;
    public $image;
    private $parent;

    /**
     * Category constructor.
     * @param $id
     */
    function __construct($id)
    {
        $this->get_by_id($id);
    }

    /**
     * Get the data for a category object from the database
     *
     * @param int $id Id of an category
     * @return $this The category object with its attributes
     */
    public function get_by_id(int $id): Category
    {
        try {
            global $connection;

            $sql = "SELECT * from Category where id = :id;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();
            $sql_result = $stmt->fetch();


            $this->id = $id;
            $this->name = $sql_result["str_name"];
            $this->description = $sql_result["str_description"];
            $this->image = $sql_result["str_image"];
            $this->parent = $sql_result["fk_parent"];

            return $this;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    public function get_properties(): array
    {
        try {
            global $connection;

            $id = $this->id;

            //ID was supplied

            $sql = "SELECT fk_property FROM Category_Property WHERE fk_category = :id;";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();
            $property_list = [];

            foreach ($stmt->fetchAll() as $current_property) {
                //print_r($article);
                $property = $current_property['fk_property'];
                //$category->get_by_id($sql_result['id']);
                array_push($property_list, $property);
            }
            return $property_list;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }


    /**
     * Returns the id of the category
     *
     * @return mixed id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Returns the name of the category
     *
     * @return mixed name
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * Returns the description of the category
     *
     * @return mixed description
     */
    public function get_description()
    {
        return $this->description;
    }

    /**
     * Returns an image which represents the category
     *
     * @return mixed base64 image
     */
    public function get_image()
    {
        return $this->image;
    }

    /**
     * Returns the parent id (also a category) of the category
     *
     * @return mixed
     */
    public function get_parent()
    {
        return $this->parent;
    }
}

