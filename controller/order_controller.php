<?php



require_once($_SERVER['DOCUMENT_ROOT'] . "/database/connection.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/model/Order.php");
if (!isset($connection)) {
    establish_db_connection();
}

/**
 * If file is called with a XMLHttpRequest, check if a specific attribute is set.
 */
if (isset($_REQUEST["mark_as_shipped"])) {
    $oc = new order_controller();
    $oc->mark_order_as_shipped($_REQUEST["mark_as_shipped"]);
    echo json_encode(["status" => true]);
}

if (isset($_REQUEST["pay_order"])) {
    $oc = new order_controller();
    $oc->pay_order($_REQUEST["order"]);
    echo json_encode(["status" => true]);
}

class order_controller
{
    /**
     * Get all orders for s a specific user.
     *
     * @param User $user User to check for orders.
     * @return array All orders of the user.
     */
    function get_orders_for_user(User $user): array
    {
        try {
            $result_array = [];

            //ID was supplied
            if (is_numeric($user)) {
                $id = $user;
            } else {
                $id = $user->id;
            }

            global $connection;
            $sql = "SELECT id, date_order_date FROM `Order` WHERE fk_user = :id ORDER BY `date_order_date` DESC";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();
            $query = $stmt->fetchALl();

            foreach ($query as $sql_result) {
                array_push($result_array, new Order($sql_result["id"]));
            }

            return $result_array;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Get latest order of an user.
     *
     * @param User $user User to check.
     * @return Order Latest order.
     */
    function get_newest_order_for_user(User $user): Order
    {
        try {
            //ID was supplied
            if (is_numeric($user)) {
                $id = $user;
            } else {
                $id = $user->id;
            }

            global $connection;
            $sql = "SELECT id, date_order_date FROM `Order` WHERE fk_user = :id  ORDER BY `date_order_date` DESC LIMIT 1";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();

            $sql_result = $stmt->fetch();
            return new Order($sql_result["id"]);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Get the latest order for a given organisation.
     *
     * @param Organization $org The organization whose last order would like to be viewed.
     * @return Order The latest order.
     */
    function get_newest_order_for_organisation(Organization $org): Order
    {
        try {
            //ID was supplied
            if (is_numeric($org)) {
                $id = $org;
            } else {
                $id = $org->id;
            }

            global $connection;
            $sql = "SELECT o.id, date_order_date FROM `Order` o join `Order_Article` oa on (o.id = oa.fk_order) join `Article` a on (oa.fk_article = a.id) WHERE a.fk_organization = :org_id ORDER BY `date_order_date` DESC LIMIT 1;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':org_id', $id);
            $stmt->execute();

            $sql_result = $stmt->fetch();
            return new Order($sql_result["id"]);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Get all orders for an organization.
     *
     * @param int $org The organization.
     * @return array Array of all orders.
     */
    function get_orders_for_organization(int $org): array
    {
        try {
            //ID was supplied
            $id = $org;

            global $connection;
            $sql = "SELECT DISTINCT o.id, date_order_date FROM `Order` o join `Order_Article` oa on (o.id = oa.fk_order) join `Article` a on (oa.fk_article = a.id) WHERE a.fk_organization = :org_id ORDER BY `date_order_date` DESC;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':org_id', $id);
            $stmt->execute();

            $query = $stmt->fetchALl();

            $result_array = [];

            foreach ($query as $sql_result) {
                array_push($result_array, new Order($sql_result["id"]));
            }

            return $result_array;

        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Create a new order for a logged in user.
     *
     * @return mixed The id of the new order.
     */
    function create_order($user_id = null)
    {
        try {
            global $connection;

            // Get current timestamp (needed for getting order_id
            $time_sql = "SELECT CURRENT_TIMESTAMP AS current_date_time";
            $time_stmt = $connection->prepare($time_sql);
            $time_stmt->execute();
            $timestamp = $time_stmt->fetch()[0];

            // Create-SQL
            if (is_null($user_id))
                $user_id = $_SESSION["user"]->id;


            $sql = "INSERT INTO `Order` (date_order_date, float_applied_rebate, fk_user,bool_paid ,fk_state) 
                    VALUES (:cur_timestamp, 0, :user_id, 0, 1)";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":cur_timestamp", $timestamp);
            $stmt->bindparam(":user_id", $user_id);
            $stmt->execute();

            // return id of order
            $id_sql = "SELECT `id` FROM `Order` ORDER BY `id` DESC LIMIT 1;";
            $id_stmt = $connection->prepare($id_sql);
            $id_stmt->execute();

            return $id_stmt->fetch()[0];

        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Connect an article to a given order.
     *
     * @param int $order_id Id of an order.
     * @param Article $article Article to add to the order.
     * @param int $amount Amount of articles added to the order.
     * @return bool Bool if function has been successful.
     */
    function add_article_to_order(int $order_id, Article $article, int $amount): bool
    {
        try {
            global $connection;
            $article_id = $article->get_id();
            $article_price = $article->get_current_price();
            $sql = "INSERT INTO `Order_Article`(fk_order, fk_article, float_price, int_amount) VALUES (:order_id, :article_id, :article_price, :amount)";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":order_id", $order_id);
            $stmt->bindparam(":article_id", $article_id);
            $stmt->bindparam(":article_price", $article_price);
            $stmt->bindparam(":amount", $amount);
            $stmt->execute();
            return true;

        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Mark an order as shipped to the customer.
     *
     * @param int $id ID of order.
     */
    function mark_order_as_shipped(int $id)
    {
        try {
            global $connection;
            $sql = "UPDATE marketplace_db.`Order` t SET t.fk_state = (SELECT id from Order_State WHERE str_state = 'Shipped'), t.date_shipping_date = CURDATE() WHERE t.id = :id;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Mark an order as complete.
     *
     * @param int $id ID of order.
     */
    function mark_order_as_completed(int $id)
    {
        try {
            global $connection;
            $sql = "UPDATE marketplace_db.`Order` t SET t.fk_state = (SELECT id from Order_State WHERE str_state = 'Completed') WHERE t.id = :id;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Check whether order has been paid.
     *
     * @param Order $order Concerning order.
     * @return mixed True or false.
     */
    function order_paid(Order $order)
    {
        $order_id = $order->id;
        global $connection;
        $sql = "SELECT `bool_paid` FROM `Order` WHERE `id` = :id";

        $stmt = $connection->prepare($sql);
        $stmt->bindparam(':id', $order_id);
        $stmt->execute();

        return $stmt->fetch()[0];
    }

    /**
     * Mark order as paid.
     * This must be replaced with a real payment method.
     *
     * @param int $order_id Concerning order.
     */
    function pay_order(int $order_id)
    {
        try {
            global $connection;
            $sql = "UPDATE `Order` SET `bool_paid` = 1 WHERE `id` = :id";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $order_id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }
}