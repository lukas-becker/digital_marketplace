<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/database/connection.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/model/Address.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/model/Article.php");

if (!isset($connection)) {
    establish_db_connection();
}

class User
{
    /**
     * Attributes of an user
     */
    public $id;
    public $e_mail;
    public $first_name;
    public $last_name;
    public $public_name;
    public $image;
    public $organization;
    private $vip;
    private $primary_address;
    private $all_addresses = [];
    private $wishlist = [];
    private $shopping_basket = [];
    private $site_admin;

    /**
     * User constructor.
     * @param $id
     */
    function __construct($id)
    {
        $this->get_by_id($id);
    }

    /**
     * Get the data for an user object from the database
     *
     * @param int $id Id of an user
     */
    private function get_by_id(int $id): void
    {
        try {
            global $connection;
            $sql = "SELECT id, str_first_name, str_last_name, str_public_name, str_profile_picture, str_e_mail, fk_organization from User where id = :id;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();

            $sql_result = $stmt->fetch();

            $this->id = $sql_result['id'];
            $this->e_mail = $sql_result['str_e_mail'];
            $this->first_name = $sql_result['str_first_name'];
            $this->last_name = $sql_result['str_last_name'];
            $this->public_name = $sql_result['str_public_name'];
            $this->image = $sql_result['str_profile_picture'];
            $this->organization = $sql_result['fk_organization'];

            return;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Get primary address for an user
     *
     * @return mixed Address object which represents the primary address
     * @throws Exception if the user has an invalid id
     */
    public function get_primary_address()
    {
        if ($this->primary_address == null) {
            $this->get_all_addresses();
        }

        return $this->primary_address;
    }

    /**
     * Returns all addresses assigned to the user
     *
     * @return array of address objects
     * @throws Exception if the user has an invalid id
     */
    public function get_all_addresses(): array
    {
        if ($this->id == null) {
            throw new Exception("You need to initialize the User Object");
        }

        if ($this->all_addresses == []) {
            try {
                global $connection;

                $sql = "SELECT id, bool_primary from Address where fk_user = :id;";

                $stmt = $connection->prepare($sql);
                $stmt->bindparam(':id', $this->id);
                $stmt->execute();

                foreach ($stmt->fetchAll() as $sql_result) {

                    $address = new Address($sql_result['id']);
                    array_push($this->all_addresses, $address);

                    if ($sql_result['bool_primary']) {
                        $this->primary_address = $address;
                    }
                }
            } catch (PDOException $e) {
                throw new PDOException($e->getMessage());
            }
        }

        return $this->all_addresses;
    }

    /**
     * @param $address A new address object added to the user
     * @return Result
     */
    public function add_address($address)
    {
        return array_push($this->all_addresses, $address);
    }

    /**
     * Check if user is an admin
     *
     * @return bool true = is admin ; false = Is not admin
     */
    public function get_is_admin(): bool
    {
        //don't store this information - always poll for changes
        try {
            global $connection;
            $sql = "SELECT count(*) as amount from Site_Admin where fk_user = :id;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $this->id);
            $stmt->execute();

            if ($stmt->fetchAll()["amount"] == 1) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    public function get_is_vip()
    {
            try {
                global $connection;
                $sql = "SELECT ROUND(SUM(float_price * int_amount)) amount FROM `Order` o JOIN Order_Article oa on o.id = oa.fk_order WHERE fk_user = :user AND date_order_date BETWEEN (CURDATE() - INTERVAL 6 MONTH) AND CURDATE() + INTERVAL 1 DAY;";

                $stmt = $connection->prepare($sql);
                $stmt->bindparam(':user', $this->id);
                $stmt->execute();

                if ($stmt->fetch()["amount"] > 250) {
                    $this->vip = true;
                } else {
                    $this->vip = false;
                }
            } catch (PDOException $e) {
                throw new PDOException($e->getMessage());
            }
        return $this->vip;
    }

    public function get_id()
    {
        return $this->id;
    }

    /**
     * Reassigns the attributes to the object
     *
     * @throws Exception If id is not valid
     */
    public function refresh_from_db()
    {
        if ($this->id == null) {
            throw new Exception("You need to initialize the User Object");
        }
        $this->all_addresses = [];
        $this->wishlist = [];
        $this->shopping_basket = [];
        $this->get_by_id($this->id);
    }

    /**
     * Check if user is an site administrator.
     * @return bool Result.
     */
    public function is_site_admin()
    {
        global $connection;

        $sql = "SELECT count(*) from Site_Admin where fk_user = :id;";

        $stmt = $connection->prepare($sql);
        $stmt->bindparam(':id', $this->id);
        $stmt->execute();
        $res = $stmt->fetch();

        return ($res[0] == 1);
    }
}