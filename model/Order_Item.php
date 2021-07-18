<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/model/Article.php";


class Order_Item
{
    /**
     * Attributes of an order item
     */
    public $article;
    public $price;
    public $amount;
    private $id;

    /**
     * Order_Item constructor.
     * @param $id
     */
    public function __construct($id)
    {
        $this->get_by_id($id);
    }

    /**
     * Get the data for an order item object from the database
     *
     * @param int $id Id of an order item
     * @return $this The order item object with its attributes
     */
    function get_by_id(int $id): Order_Item
    {
        try {
            global $connection;
            $sql = "SELECT id, fk_article, float_price, int_amount from Order_Article where id = :id;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();

            $sql_result = $stmt->fetch();

            $this->id = $sql_result['id'];
            $this->article = new Article($sql_result['fk_article']);
            $this->price = $sql_result['float_price'];
            $this->amount = $sql_result['int_amount'];

            return $this;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }
}