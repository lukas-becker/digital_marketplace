<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/database/connection.php");
if (!isset($connection)) {
    establish_db_connection();
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/model/Organization.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/model/Article.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/model/User.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/controller/user_controller.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/php_backend/image_pipeline.php");
session_start();

// If user is logged in and has an organization, save its value
if ($_SESSION["user"]->organization != 0) {
    $org = new Organization($_SESSION["user"]->organization);
}


// If file is called with an XMLHttpRequest, check if a specific attribute is set
if (isset($_REQUEST)) { //Always true, but i can collapse this block
    if (isset($_REQUEST["set_profile_picture"])) {
        $image_pipe = new image_pipeline();
        $image = $image_pipe->squared_pipeline($_FILES['upload']['tmp_name'], $_FILES['upload']["type"]);
        $organization_controller = new organization_controller();
        try {
            if ($organization_controller->set_organization_picture($image)) {
                $resultArray = ["status" => true, "image" => $image];
            } else {
                $resultArray = ["status" => false];
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);

    } else if (isset($_REQUEST["set_address"])) {
        $organization_controller = new organization_controller();
        if ($organization_controller->set_address($_REQUEST["street"], $_REQUEST["nr"], $_REQUEST["zip"], $_REQUEST["city"])) {
            $resultArray = ["status" => true];
        } else {
            $resultArray = ["status" => false];
        }
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);

    } else if (isset($_REQUEST["set_name"])) {
        $organization_controller = new organization_controller();
        if ($organization_controller->set_name($_REQUEST["name"])) {
            $resultArray = ["status" => true];
        } else {
            $resultArray = ["status" => false];
        }
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);

    } else if (isset($_REQUEST["set_description"])) {
        $organization_controller = new organization_controller();
        if ($organization_controller->set_description($_REQUEST["description"])) {
            $resultArray = ["status" => true];
        } else {
            $resultArray = ["status" => false];
        }
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);

    } else if (isset($_REQUEST["remove_user"])) {
        $organization_controller = new organization_controller();
        if ($organization_controller->remove_user($_REQUEST["user"])) {
            $resultArray = ["status" => true];
        } else {
            $resultArray = ["status" => false];
        }
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);

    } else if (isset($_REQUEST["add_user"])) {
        $organization_controller = new organization_controller();
        if ($organization_controller->add_user($_REQUEST["user"])) {
            $resultArray = ["status" => true];
        } else {
            $resultArray = ["status" => false];
        }
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);

    } else if (isset($_REQUEST["new_org"])) {
        $organization_controller = new organization_controller();
        $organization_controller->new_organization($_REQUEST["nameInput"], $_REQUEST["orgPictureText"], $_REQUEST["street"], $_REQUEST["nr"], $_REQUEST["zip"], $_REQUEST["city"]);
        header("Location: /index.php?message=new_org");
        die();
    }
}


class organization_controller
{
    /**
     * Get number of sales for the current month.
     *
     * @param Organization $organization Concerning organization.
     * @return mixed Number.
     */
    function get_sales_for_current_month(Organization $organization)
    {
        global $connection;
        $id = 0;
        if (is_numeric($organization)) {
            $id = $organization;
        } else {
            $id = $organization->id;
        }

        $sql = "SELECT count(DISTINCT o.id) as amount from `Order` o join Order_Article OA on o.id = OA.fk_order join Article A on OA.fk_article = A.id join Organization org on A.fk_organization = org.id WHERE org.id = :id AND date_order_date BETWEEN (CURRENT_DATE() - INTERVAL 1 MONTH) AND (CURRENT_DATE() + INTERVAL 1 DAY);";
        $stmt = $connection->prepare($sql);
        $stmt->bindparam(":id", $id);
        $stmt->execute();

        return $stmt->fetch()["amount"];

    }

    /**
     * Get percentage describing the development of sales numbers.
     *
     * @param Organization $organization Concerning organization.
     * @return float|int Number.
     */
    function get_sales_trend(Organization $organization)
    {

        global $connection;
        if (is_numeric($organization)) {
            $id = $organization;
        } else {
            $id = $organization->id;
        }

        $current_month = $this->get_sales_for_current_month($organization);

        $sql = "SELECT COUNT(DISTINCT o.id) as amount from `Order` o join Order_Article OA on o.id = OA.fk_order join Article A on OA.fk_article = A.id join Organization org on A.fk_organization = org.id WHERE org.id = :id AND date_order_date BETWEEN (CURRENT_DATE() - INTERVAL 2 MONTH) AND (CURRENT_DATE() - INTERVAL 1 MONTH);";
        $stmt = $connection->prepare($sql);
        $stmt->bindparam(":id", $id);
        $stmt->execute();

        $last_month = $stmt->fetch()["amount"];

        if ($last_month == 0) {
            return 100;
        }

        //Percentage
        return ($current_month / $last_month - 1) * 100;

    }

    /**
     * Get number of articles sales for the current month.
     *
     * @param Organization $organization Concerning organization.
     * @return mixed Number.
     */
    function get_article_sold_count_for_current_month(Organization $organization)
    {
        global $connection;
        if (is_numeric($organization)) {
            $id = $organization;
        } else {
            $id = $organization->id;
        }

        $sql = "SELECT IFNULL(SUM(int_amount),0) as amount from `Order` o join Order_Article OA on o.id = OA.fk_order join Article A on OA.fk_article = A.id join Organization org on A.fk_organization = org.id WHERE org.id = :id AND date_order_date BETWEEN (CURRENT_DATE() - INTERVAL 1 MONTH) AND (CURRENT_DATE() + INTERVAL 1 DAY);
";
        $stmt = $connection->prepare($sql);
        $stmt->bindparam(":id", $id);
        $stmt->execute();

        return $stmt->fetch()["amount"];

    }

    /**
     * Get percentage describing the development of article sale numbers.
     *
     * @param Organization $organization Concerning organization.
     * @return float|int Number.
     */
    function get_article_trend(Organization $organization)
    {
        global $connection;
        if (is_numeric($organization)) {
            $id = $organization;
        } else {
            $id = $organization->id;
        }

        $current_month = $this->get_article_sold_count_for_current_month($organization);

        $sql = "SELECT IFNULL(SUM(int_amount),0) as amount from `Order` o join Order_Article OA on o.id = OA.fk_order join Article A on OA.fk_article = A.id join Organization org on A.fk_organization = org.id WHERE org.id = :id AND date_order_date BETWEEN (CURRENT_DATE() - INTERVAL 2 MONTH) AND (CURRENT_DATE() - INTERVAL 1 MONTH);";
        $stmt = $connection->prepare($sql);
        $stmt->bindparam(":id", $id);
        $stmt->execute();

        $last_month = $stmt->fetch()["amount"];

        if ($last_month == 0) {
            return 100;
        }

        //Percentage
        return ($current_month / $last_month - 1) * 100;

    }

    /**
     * Get amount of revenue for the current month.
     *
     * @param Organization $organization Concerning organization.
     * @return mixed Number.
     */
    function get_revenue_for_current_month(Organization $organization)
    {
        global $connection;
        if (is_numeric($organization)) {
            $id = $organization;
        } else {
            $id = $organization->id;
        }

        $sql = "SELECT IFNULL(SUM(float_price * int_amount),0) as amount from `Order` o join Order_Article OA on o.id = OA.fk_order join Article A on OA.fk_article = A.id join Organization org on A.fk_organization = org.id WHERE org.id = :id AND date_order_date BETWEEN (CURRENT_DATE() - INTERVAL 1 MONTH) AND (CURRENT_DATE() + INTERVAL 1 DAY);";
        $stmt = $connection->prepare($sql);
        $stmt->bindparam(":id", $id);
        $stmt->execute();

        return $stmt->fetch()["amount"];

    }

    /**
     * Get percentage describing the development of revenue numbers.
     *
     * @param Organization $organization Concerning organization.
     * @return float|int Number.
     */
    function get_revenue_trend(Organization $organization)
    {
        global $connection;
        if (is_numeric($organization)) {
            $id = $organization;
        } else {
            $id = $organization->id;
        }

        $current_month = $this->get_revenue_for_current_month($organization);

        $sql = "SELECT IFNULL(SUM(float_price * int_amount),0) as amount from `Order` o join Order_Article OA on o.id = OA.fk_order join Article A on OA.fk_article = A.id join Organization org on A.fk_organization = org.id WHERE org.id = :id AND date_order_date BETWEEN (CURRENT_DATE() - INTERVAL 2 MONTH) AND (CURRENT_DATE() - INTERVAL 1 MONTH);";
        $stmt = $connection->prepare($sql);
        $stmt->bindparam(":id", $id);
        $stmt->execute();

        $last_month = $stmt->fetch()["amount"];

        if ($last_month == 0) {
            return 100;
        }

        //Percentage
        return ($current_month / $last_month - 1) * 100;

    }

    /**
     * Returns the most sold product.
     *
     * @param Organization $organization Concerning organization.
     * @return Article Article object.
     */
    function get_most_selling_product(Organization $organization): Article
    {
        global $connection;
        if (is_numeric($organization)) {
            $id = $organization;
        } else {
            $id = $organization->id;
        }

        $sql = "SELECT A.id FROM Article A join Order_Article OA on A.id = OA.fk_article WHERE A.fk_organization = :org_id GROUP BY A.id ORDER BY sum(int_amount) DESC LIMIT 1;";
        $stmt = $connection->prepare($sql);
        $stmt->bindparam(":org_id", $id);
        $stmt->execute();

        return new Article($stmt->fetch()["id"]);

    }

    /**
     * Set a new Picture for the organization.
     *
     * @param string $newPicture Picture as base64 string.
     * @return mixed Confirmation.
     * @throws Exception MYSQL errors.
     */
    function set_organization_picture(string $newPicture)
    {
        try {
            global $org;
            global $connection;

            $org->image = $newPicture;

            $sql = "UPDATE `Organization` SET str_organization_picture = :image WHERE id = :org_id;";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":image", $newPicture);
            $stmt->bindparam(":org_id", $org->id);
            $query = $stmt->execute();
            $org->refresh_from_db();

            return $query;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Get list of users in the current organization.
     *
     * @return array Array of user information.
     */
    function get_users_in_organization(): array
    {
        global $connection;
        global $org;

        $sql = "SELECT str_first_name, str_last_name, str_e_mail, U.id FROM Organization O join User U on O.id = U.fk_organization WHERE O.id = :id;";
        $stmt = $connection->prepare($sql);
        $stmt->bindparam(":id", $org->id);
        $stmt->execute();

        $result_array = [];
        foreach ($stmt->fetchAll() as $res) {
            array_push($result_array, [$res['str_first_name'], $res["str_last_name"], $res["str_e_mail"], $res["id"]]);
        }

        return $result_array;
    }

    /**
     * Set an address for an organization.
     *
     * @param string $street New street.
     * @param int $nr New number.
     * @param int $zip New zip.
     * @param string $city New city.
     * @return mixed Confirmation.
     */
    function set_address(string $street, int $nr, int $zip, string $city)
    {
        try {
            global $org;
            global $connection;

            $sql = "UPDATE `Organization` SET str_street = :street, str_nr = :nr, str_zip = :zip, str_city = :city WHERE id = :org_id;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':street', $street);
            $stmt->bindparam(':nr', $nr);
            $stmt->bindparam(':zip', $zip);
            $stmt->bindparam(':city', $city);
            $stmt->bindparam(':org_id', $org->id);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException(($e->getMessage()));
        }
    }

    /**
     * Set a new organization name.
     *
     * @param string $name New name.
     * @return mixed Confirmation.
     */
    function set_name(string $name)
    {
        try {
            global $connection;
            global $org;

            $sql = "UPDATE `Organization` SET str_name = :new_name WHERE id = :org_id;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':new_name', $name);
            $stmt->bindparam(':org_id', $org->id);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException(($e->getMessage()));
        }
    }

    /**
     * Set a new organization description.
     *
     * @param string $desc New description.
     * @return mixed Confirmation.
     */
    function set_description(string $desc)
    {
        try {
            global $connection;
            global $org;

            $sql = "UPDATE `Organization` SET str_description = :new_desc WHERE id = :org_id;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':new_desc', $desc);
            $stmt->bindparam(':org_id', $org->id);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException(($e->getMessage()));
        }
    }

    /**
     * Remove user from organization.
     *
     * @param int $user User to remove.
     * @return mixed Confirmation.
     */
    function remove_user(int $user)
    {
        try {
            global $connection;

            $sql = "UPDATE `User` SET fk_organization = null WHERE id = :user_id;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':user_id', $user);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException(($e->getMessage()));
        }
    }

    /**
     * Add user to organization.
     *
     * @param string $mail Mail of user to add.
     * @return mixed Confirmation.
     */
    function add_user($mail)
    {
        try {
            global $connection;
            global $org;

            $sql = "UPDATE `User` SET fk_organization = :org_id WHERE str_e_mail = :user_mail;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':org_id', $org->id);
            $stmt->bindparam(':user_mail', $mail);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException(($e->getMessage()));
        }
    }

    /**
     * Create a new organization.
     *
     * @param string $name Name.
     * @param string $image Image.
     * @param string $street Street.
     * @param string $nr Number.
     * @param string $zip Zip.
     * @param string $city City.
     * @return bool Confirmation.
     */
    function new_organization(string $name, string $image, string $street, string $nr, string $zip, string $city): bool
    {
        global $connection;

        $sql = "INSERT INTO marketplace_db.Organization (str_name, str_organization_picture, str_street, str_nr,str_zip, str_city)
                VALUES (:org_name, :image, :street, :nr, :zip, :city);";
        $stmt = $connection->prepare($sql);
        $stmt->bindparam(':org_name', $name);
        $stmt->bindparam(':image', $image);
        $stmt->bindparam(':street', $street);
        $stmt->bindparam(':nr', $nr);
        $stmt->bindparam(':zip', $zip);
        $stmt->bindparam(':city', $city);

        $stmt->execute();

        $sql = "SELECT id FROM Organization ORDER BY id DESC";
        $stmt = $connection->prepare($sql);
        $stmt->execute();

        $id = $stmt->fetch()["id"];

        $sql = "UPDATE `User` SET fk_organization = :org_id WHERE id = :user;";

        $stmt = $connection->prepare($sql);
        $stmt->bindparam(':org_id', $id);
        $stmt->bindparam(':user', $_SESSION["user"]->id);
        $stmt->execute();

        $user_controller = new user_controller();
        $user_controller->logout_user();


        return true;
    }
}