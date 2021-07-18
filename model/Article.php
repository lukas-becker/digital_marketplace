<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/database/connection.php");

if (!isset($connection)) {
    establish_db_connection();
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/controller/category_controller.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/controller/order_controller.php");


class Article
{
    /**
     * Attributes of an article
     */
    private $id;
    private $title;
    private $description;
    private $location;
    private $days_until_shipping;
    private $current_available;
    private $current_price;
    private $organization;
    private $shipping_cost;
    private $images = [];
    private $categories = [];
    private $properties = [];
    public $auction;
    public $auction_end_date;
    private $visible;

    /**
     * Article constructor.
     * @param $id
     */
    function __construct($id)
    {
        $this->get_by_id($id);
    }

    /**
     * Get the data for an article object from the database
     *
     * @param int $id Id of the Article
     * @return $this The article object with its attributes
     */
    public function get_by_id(int $id): Article
    {
        try {
            global $connection;

            $sql = "SELECT * from Article where id = :id;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();
            $sql_result = $stmt->fetch();


            $this->id = $id;
            $this->title = $sql_result["str_title"];
            $this->description = $sql_result["str_description"];
            $this->location = $sql_result["str_location"];
            $this->days_until_shipping = $sql_result["int_days_until_shipping"];
            $this->current_available = $sql_result["int_current_available"];
            $this->current_price = $sql_result["float_current_price"];
            $this->organization = $sql_result["fk_organization"];
            $this->shipping_cost = $sql_result['float_shipping_cost'];
            $this->auction = $sql_result['bool_auction'];
            $this->auction_end_date = $sql_result["date_auction_end"];
            $this->visible = $sql_result["bool_visible"];


            return $this;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Returns the first image that is stored for an article
     *
     * @return mixed base64 of the first image for an article
     * @throws Exception If article object has no valid id (is not initialized)
     */
    public function get_first_image()
    {
        if ($this->id == null) {
            throw new Exception("You need to initialize the Article Object");
        }

        if ($this->images != []) {
            return $this->images[0];
        }

        try {
            global $connection;
            $sql = "SELECT str_image from Article_Images where fk_article = :id LIMIT 1;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $this->id);
            $stmt->execute();

            $sql_result = $stmt->fetch();
            return $sql_result["str_image"];
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }

    }

    /**
     * Returns all images for an article
     *
     * @return array which stores all images for an article
     * @throws Exception If article object has no valid id (is not initialized)
     */
    public function get_images(): array
    {
        if ($this->id == null) {
            throw new Exception("You need to initialize the Article Object");
        }

        if ($this->images == []) {
            try {
                global $connection;
                $sql = "SELECT * from Article_Images where fk_article = :id;";

                $stmt = $connection->prepare($sql);
                $stmt->bindparam(':id', $this->id);
                $stmt->execute();

                foreach ($stmt->fetchAll() as $sql_result) {
                    array_push($this->images, $sql_result["str_image"]);
                }
            } catch (PDOException $e) {
                throw new PDOException($e->getMessage());
            }
        }

        return $this->images;
    }

    /**
     * Return all categories for an article
     *
     * @return array of categories for an article
     * @throws Exception If article object has no valid id (is not initialized)
     */
    public function get_categories(): array
    {
        if ($this->id == null) {
            throw new Exception("You need to initialize the Article Object");
        }

        if ($this->categories == []) {
            try {
                global $connection;

                $sql = "SELECT fk_category from Article_Category where fk_article = :id;";

                $stmt = $connection->prepare($sql);
                $stmt->bindparam(':id', $this->id);
                $stmt->execute();

                $categoryList = [];

                foreach ($stmt->fetchAll() as $sql_result) {
                    $category = new Category($sql_result['id']);
                    array_push($categoryList, $category);
                }

                $this->categories = $categoryList;
            } catch (PDOException $e) {
                throw new PDOException($e->getMessage());
            }
        }


        return $this->categories;
    }

    public function get_properties(): array
    {
        try {
            global $connection;

            $id = $this->id;

            //ID was supplied

            $sql = "SELECT fk_property FROM Article_Property WHERE fk_article = :id;";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();
            $property_list = [];

            foreach ($stmt->fetchAll() as $current_property) {
                $property = new Property($current_property['fk_property']);
                array_push($property_list, $property);
            }
            return $property_list;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Returns id of an article
     *
     * @return mixed id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Get highest bid or false.
     * @return false|mixed Result.
     */
    public function get_latest_bid()
    {
        if (!$this->auction) {
            return false;
        }

        global $connection;

        $sql = "SELECT float_amount from Bids WHERE fk_article = :article ORDER BY ID DESC LIMIT 1";
        $stmt = $connection->prepare($sql);
        $stmt->bindparam(":article", $this->id);
        $stmt->execute();
        $result = $stmt->fetch()["float_amount"];

        if (is_null($result)) {
            return false;
        } else {
            return $result;
        }
    }

    /**
     * Returns highest bidder id or error.
     * @return false|mixed Result.
     */
    public function get_highest_bidder()
    {
        if (!$this->auction) {
            return false;
        }

        global $connection;

        $sql = "SELECT fk_user from Bids WHERE fk_article = :article ORDER BY ID DESC LIMIT 1";
        $stmt = $connection->prepare($sql);
        $stmt->bindparam(":article", $this->id);
        $stmt->execute();
        $result = $stmt->fetch()["fk_user"];

        if (is_null($result)) {
            return false;
        } else {
            return $result;
        }
    }

    /**
     * Returns time string with remaining time
     * @return string Result
     * @throws Exception Date exception.
     */
    public function get_remaining_auction_time()
    {
        $now = new DateTime();
        $future_date = new DateTime($this->auction_end_date);

        $interval = $future_date->diff($now);

        if ($now > $future_date) {
            $this->finalize_auction();
            return "Auction has ended!";
        } else {
            return $interval->format("%a days, %h hours,<br> %i minutes, %s seconds");
        }

    }

    /**
     * Create order after auction has ended
     */
    private function finalize_auction()
    {

        global $connection;

        //Check of the order was already created
        $sql = "SELECT COUNT(*) FROM Order_Article WHERE fk_article = " . $this->id . ";";
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetch();

        //Continue if not created
        if ($res[0] == 0) {
            //Gather information
            $sql = "SELECT fk_user, float_amount from Bids WHERE fk_article = :art ORDER BY ID DESC LIMIT 1";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":art", $this->id);
            $stmt->execute();
            $res = $stmt->fetch();

            //Create order
            $order_controller = new order_controller();
            $order_id = $order_controller->create_order($res["fk_user"]);

            //Correct order date
            $sql = "UPDATE Order set date_order_date = :order_date WHERE id = :order_id;";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":order_date", $this->auction_end_date);
            $stmt->bindparam(":order_id", $order_id);
            $stmt->execute();

            //Add auction article.
            $order_controller->add_article_to_order($order_id, $this->id, $res["float_amount"], 1);

            $sql = "UPDATE Article SET bool_visible = 0 WHERE id = " . $this->id;
            $stmt = $connection->prepare($sql);
            $stmt->execute();
        }

    }


    /**
     * Returns title of an article
     *
     * @return mixed title
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * Returns description of an article
     *
     * @return mixed description
     */
    public function get_description()
    {
        return $this->description;
    }

    /**
     * Returns location of an article
     *
     * @return mixed location
     */
    public function get_location()
    {
        return $this->location;
    }

    /**
     * Returns days until shipping of an article
     *
     * @return mixed days until the article is delivered
     */
    public function get_days_until_shipping()
    {
        return $this->days_until_shipping;
    }

    /**
     * Returns the amount of an article
     *
     * @return mixed amount
     */
    public function get_current_available()
    {
        return $this->current_available;
    }

    /**
     * Returns current price of an article
     *
     * @return mixed current price
     */
    public function get_current_price()
    {
        return $this->current_price;
    }

    /**
     * Returns organization id of an article
     *
     * @return mixed organization id
     */
    public function get_organization()
    {
        return $this->organization;
    }

    /**
     * Returns shipping cost of an article
     *
     * @return mixed shipping cost
     */
    public function get_shipping_cost()
    {
        return $this->shipping_cost;
    }

    /**
     * @return mixed
     */
    public function get_visible()
    {
        return $this->visible;
    }

}

