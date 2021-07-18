<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/model/Order_Item.php";


class Order
{
    /**
     * Attributes of an order
     */
    public $id;
    public $order_date;
    public $applied_rebate;
    public $state;
    public $shipping_date;
    public $articles = [];
    private $seller;
    public $paid;

    /**
     * Order constructor.
     * @param $id
     */
    public function __construct($id)
    {
        $this->get_by_id($id);
    }

    /**
     * Get the data for an order object from the database
     *
     * @param int $id Id of an order
     * @return $this The order object with its attributes
     */
    function get_by_id(int $id): Order
    {
        try {
            global $connection;
            $sql = "SELECT o.id, o.date_order_date, o.float_applied_rebate, o.bool_paid, os.str_state, o.date_shipping_date 
                    FROM `Order` o LEFT JOIN Order_State os ON o.fk_state = os.id WHERE o.id = :id;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();

            $sql_result = $stmt->fetch();

            $this->id = $sql_result['id'];
            $this->order_date = $sql_result['date_order_date'];
            $this->applied_rebate = $sql_result['float_applied_rebate'];
            $this->paid = $sql_result['bool_paid'];
            $this->state = $sql_result["str_state"];
            $this->shipping_date = $sql_result["date_shipping_date"] ?? 0;

            $sql = "SELECT id FROM Order_Article WHERE fk_order = :id;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $this->id);
            $stmt->execute();

            foreach ($stmt->fetchAll() as $sql_result) {
                array_push($this->articles, new Order_Item($sql_result["id"]));
            }

            if ($this->state == "Shipped") {
                $days_until_shipping_max = 0;
                foreach ($this->articles as $order_item) {
                    if ($order_item->article->get_days_until_shipping() > $days_until_shipping_max) {
                        $days_until_shipping_max = $order_item->article->get_days_until_shipping();
                    }
                }


                if (round((time() - strtotime($this->shipping_date)) / (60 * 60 * 24)) > $days_until_shipping_max) {
                    $order_controller = new order_controller();
                    $order_controller->mark_order_as_completed($this->id);
                    $this->state = "Completed";
                }
            }


            return $this;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Return organization who sold the articles in order.
     * @return Organization Object.
     */
    function get_seller()
    {
        if ($this->seller == null) {
            $this->seller = new Organization($this->articles[0]->article->get_organization());
        }

        return $this->seller;
    }


}