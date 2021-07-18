<?php



require_once($_SERVER['DOCUMENT_ROOT'] . "/database/connection.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/model/Address.php");
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/User.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/php_backend/image_pipeline.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/controller/address_controller.php";
session_start();

if (!isset($connection)) {
    establish_db_connection();
}

// If file is called with a XMLHttpRequest or on page load, check if a specific attribute is set
if (isset($_REQUEST)) {
    if (isset($_REQUEST["login"])) {
        $resultArray = array();
        $user_controller = new user_controller();
        $res = $user_controller->login_mail_password($_REQUEST['mail'], $_REQUEST['password'], $_REQUEST['fingerprint']);
        if ($res) {
            $resultArray = ["status" => true, "first_name" => $_SESSION["user"]->first_name, "picture" => $_SESSION["user"]->image];
            echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
        } else {
            $resultArray = ["status" => false];
            echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
        }

    }

    if (isset($_REQUEST["fingerprint_relogin"])) {
        $resultArray = array();
        $user_controller = new user_controller();
        $res = $user_controller->login_fingerprint($_REQUEST['fingerprint_relogin']);
        if ($res) {
            $resultArray = ["status" => true];
        } else {
            $resultArray = ["status" => false];
        }
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
    }

    if (isset($_REQUEST["logout"])) {
        $user_controller = new user_controller();
        $resultArray = ["status" => $user_controller->logout_user()];
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
    }

    if (isset($_REQUEST["set_public_name"])) {
        $user_controller = new user_controller();
        if ($user_controller->set_public_name($_REQUEST["name"])) {
            $resultArray = ["status" => true];
        } else {
            $resultArray = ["status" => false];
        }
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
    }

    if (isset($_REQUEST["set_mail"])) {
        $user_controller = new user_controller();
        if ($user_controller->set_mail($_REQUEST["mail"])) {
            $resultArray = ["status" => true];
        } else {
            $resultArray = ["status" => false];
        }
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
    }

    if (isset($_REQUEST["register"])) {
        $user_controller = new user_controller();
        $resultArray = ["status" => $user_controller->register($_REQUEST)];
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
    }

    if (isset($_REQUEST["set_profile_picture"])) {
        $image_pipe = new image_pipeline();
        $image = $image_pipe->squared_pipeline($_FILES['upload']['tmp_name'], $_FILES['upload']["type"]);
        $user_controller = new user_controller();
        if ($user_controller->set_profile_picture($image)) {
            $resultArray = ["status" => true, "image" => $image];
        } else {
            $resultArray = ["status" => false];
        }
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
    }

    if (isset($_REQUEST["add_address"])) {
        $user_controller = new user_controller();
        $resultArray = ["status" => $user_controller->add_address($_REQUEST)];
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
    }

    if (isset($_REQUEST["delete_address"])) {
        $user_controller = new user_controller();
        $resultArray = ["status" => $user_controller->delete_address($_REQUEST["id"])];
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
    }

    if (isset($_REQUEST["make_primary_address"])) {
        $user_controller = new user_controller();
        try {
            $resultArray = ["status" => $user_controller->make_primary_address($_REQUEST["make_primary_address"])];
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
    }

    if (isset($_REQUEST["set_password"])) {
        $user_controller = new user_controller();
        $resultArray = ["status" => $user_controller->set_password($_REQUEST["password"])];
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
    }

    if (isset($_REQUEST["add_to_shopping_basket"])) {
        $user_controller = new user_controller();
        $basket = $user_controller->get_shopping_basket()[0];
        $article = new Article($_REQUEST["article_id"]);
        if (in_array($article, $basket)) {
            $resultArray = ["status" => $user_controller->update_amount_of_article($_REQUEST["article_id"], $_REQUEST["article_amount"])];
        } else {
            $resultArray = ["status" => $user_controller->add_to_shopping_basket($_REQUEST["article_id"], $_REQUEST["article_amount"])];
        }
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
    }

    if (isset($_REQUEST["delete_from_shopping_basket"])) {
        $user_controller = new user_controller();
        $resultArray = ["status" => $user_controller->delete_from_shopping_basket($_REQUEST["article_id"])];
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
    }

    if (isset($_REQUEST["clear_shopping_basket"])) {
        $user_controller = new user_controller();
        $resultArray = ["status" => $user_controller->clear_basket()];
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
    }

    if (isset($_REQUEST["update_amount_article"])) {
        $user_controller = new user_controller();
        $resultArray = ["status" => $user_controller->update_amount_of_article($_REQUEST["article_id"], $_REQUEST["article_amount"])];
        array_push($resultArray, $_REQUEST["article_id"]);
        array_push($resultArray, $_REQUEST["article_amount"]);
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
    }

    if (isset($_REQUEST["number_articles_in_basket"])) {
        $user_controller = new user_controller();
        $result = $user_controller->get_number_of_articles_in_basket();
        $resultArray = ["status" => $result[0], "result" => $result[1]];
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
    }

    if (isset($_REQUEST['recalculate_shopping_basket'])) {
        $user_controller = new user_controller();
        $result = $user_controller->calculate_sum_of_basket();
        $resultArray = ["status" => $result[0], "result" => $result[1]];
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
    }
}


class user_controller
{
    /**
     * Login user with mail and password.
     *
     * @param string $mail User mail address.
     * @param string $password User password.
     * @param string $fingerprint Browser fingerprint.
     * @return bool Result.
     */
    public function login_mail_password(string $mail, string $password, string $fingerprint): bool
    {
        try {
            global $connection;

            $sql = "SELECT id, str_password_hash as password from `User` where str_e_mail = :mail AND bool_active = true";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':mail', $mail);
            $stmt->execute();

            $sql_result = $stmt->fetch();


            if (password_verify($password, $sql_result['password'])) {
                $user_id = $sql_result['id'];

                /*generate security code out of time and user email*/
                $date = date_create();
                $secure_code = date_timestamp_get($date) . $mail;
                $secure_code_hash = md5($secure_code);

                $user = new User($user_id);
                //$user->get_by_id($user_id);

                date_add($date, date_interval_create_from_date_string("30 days"));
                $sql = "INSERT INTO `Session` (fk_user, str_browser_fingerprint, str_security_code, date_valid_until) VALUES (:userid, :fingerprint, :secure_code_hash, :login_date)";
                $stmt = $connection->prepare($sql);
                $stmt->bindparam(':userid', $user->id);
                $stmt->bindparam(':fingerprint', $fingerprint);
                $stmt->bindparam(':secure_code_hash', $secure_code_hash);
                $stmt->bindparam(':login_date', date("Y-m-d", date_timestamp_get($date)));
                $stmt->execute();

                setcookie("secure_code", $secure_code_hash, time() + 2592000, "/");

                $_SESSION["login"] = true;
                $_SESSION["user"] = $user;

                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }

    }

    /**
     * Login user with browser fingerprint and cookie.
     *
     * @param string $fingerprint Browser fingerprint.
     * @return bool Result.
     */
    public function login_fingerprint(string $fingerprint): bool
    {
        try {
            global $connection;

            $sql = "SELECT count(*) as 'amount' from `User` where id = (SELECT fk_user from `Session` WHERE str_browser_fingerprint = :fingerprint and str_security_code = :security_code)";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':fingerprint', $fingerprint);
            $stmt->bindparam(':security_code', $_COOKIE["secure_code"]);
            $stmt->execute();

            if ($stmt->fetch()['amount'] == 0) {
                return false;
            } else {
                $sql = "SELECT id, str_first_name from `User` where id = (SELECT fk_user from `Session` WHERE str_browser_fingerprint = :fingerprint and str_security_code = :security_code)";

                $stmt = $connection->prepare($sql);
                $stmt->bindparam(':fingerprint', $fingerprint);
                $stmt->bindparam(':security_code', $_COOKIE["secure_code"]);
                $stmt->execute();

                $sql_result = $stmt->fetch();

                $user = new User($sql_result['id']);
                //$user = $user -> get_by_id($sql_result['id']);

                $_SESSION["login"] = true;
                $_SESSION["user"] = $user;
                return true;
            }
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Set a new display name (for reviews).
     *
     * @param string $name The new name.
     * @return mixed Confirmation.
     */
    public function set_public_name(string $name)
    {
        try {
            global $user;
            global $connection;

            if ($user == null) {
                $user = $_SESSION["user"];
            }

            $user->public_name = $name;

            $sql = "UPDATE marketplace_db.User t SET t.str_public_name = :public_name WHERE t.id = :user_id;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':public_name', $name);
            $stmt->bindparam(':user_id', $user->id);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException(($e->getMessage()));
        }
    }

    /**
     * Set a new mail address for the logged in user.
     *
     * @param string $mail New mail address.
     * @return mixed Confirmation.
     * @throws Exception MYSQL errors.
     */
    public function set_mail(string $mail)
    {
        try {
            global $user;
            global $connection;

            if ($user == null) {
                $user = $_SESSION["user"];
            }

            $user->e_mail = $mail;

            $sql = "UPDATE marketplace_db.User t SET t.str_e_mail = :mail WHERE t.id = :user_id;";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":mail", $mail);
            $stmt->bindparam(":user_id", $user->id);
            $query = $stmt->execute();
            $user->refresh_from_db();

            return $query;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Logout a logged in user.
     *
     * @return bool Confirmation.
     */
    public function logout_user(): bool
    {
        if (!isset($_SESSION["user"])) {
            return false;
        }

        global $connection;

        if (isset($_COOKIE["secure_code"])) {
            try {
                $sql = "DELETE FROM `Session` WHERE str_security_code = :security_code;";

                $stmt = $connection->prepare($sql);
                $stmt->bindparam(':security_code', $_COOKIE["secure_code"]);
                $stmt->execute();
            } catch (PDOException $e) {
                throw new PDOException($e->getMessage());
            }
        }

        $_SESSION["user"] = null;

        session_destroy();
        return true;

    }

    /**
     * Save a new user in the database.
     *
     * @param array $user Object containing relevant information.
     * @return bool Confirmation.
     */
    function register(array $user): bool
    {
        try {
            global $connection; //use in function

            $sql = "SELECT count(*) as 'amount' from `User` where str_e_mail = :user_mail";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':user_mail', $user["mail"]);
            $stmt->execute();

            if ($stmt->fetch()['amount'] == 0) {

                /*encrypt password*/
                $password = password_hash($user["password"], PASSWORD_BCRYPT);

                //Insert User
                $sql = "INSERT INTO `User` (`str_first_name`, `str_last_name`, `str_e_mail`, `str_password_hash`) VALUES (:firstname , :lastname, :user_mail, :password);";
                $stmt = $connection->prepare($sql);
                $stmt->bindparam(':firstname', $user['firstName']);
                $stmt->bindparam(':lastname', $user['lastName']);
                $stmt->bindparam(':user_mail', $user['mail']);
                $stmt->bindparam(':password', $password);
                $stmt->execute();

                //Get User ID
                $sql = "SELECT id from `User` where str_e_mail = :user_mail;";
                $stmt = $connection->prepare($sql);
                $stmt->bindparam(':user_mail', $user['mail']);
                $stmt->execute();

                $id = $stmt->fetch()['id'];

                //Insert Address
                $sql = "insert into `Address` (str_street, str_number, str_zip, str_city, fk_user, bool_primary) values (:street, :str_number, :zip, :city, :user_id, 1);";
                $stmt = $connection->prepare($sql);
                $stmt->bindparam(':street', $user['street']);
                $stmt->bindparam(':str_number', $user["nr"]);
                $stmt->bindparam(':zip', $user['zip']);
                $stmt->bindparam(':city', $user['city']);
                $stmt->bindparam(':user_id', $id);
                $stmt->execute();

                include($_SERVER['DOCUMENT_ROOT'] . "/php_backend/send_mail.php");
                send_email($user["mail"],
                    "Bestätigen Sie Ihre E-Mail Adresse",
                    "Willkommen bei",
                    "Marketplace",
                    "Nur noch ein Schritt!",
                    'Klicken Sie den folgenden Link um Ihre E-Mail-Adresse zu bestätigen.<br>Anschließend können Sie sich anmelden.',
                    "http://localhost/pages/activate_user.php?id=" . $id, "Hier klicken");

                return true;

            } else {
                return false;
            }
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Save new profile picture for user.
     *
     * @param string $newPicture New picture as base64 string.
     * @return mixed Confirmation.
     * @throws Exception MYSQL errors.
     */
    function set_profile_picture(string $newPicture)
    {
        try {
            global $user;
            global $connection;

            if ($user == null) {
                $user = $_SESSION["user"];
            }


            $user->image = $newPicture;

            $sql = "UPDATE marketplace_db.User t SET t.str_profile_picture = :image WHERE t.id = :user_id;";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":image", $newPicture);
            $stmt->bindparam(":user_id", $user->id);
            $query = $stmt->execute();
            $user->refresh_from_db();

            return $query;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Add a new address.
     *
     * @param $address Id of relevant address.
     * @return bool Confirmation.
     * @throws Exception MYSQL errors.
     */
    function add_address($address): bool
    {
        try {
            global $user;

            if ($user == null) {
                $user = $_SESSION["user"];
            }

            $address_controller = new address_controller();
            $addr = $address_controller->add_address($user, $address);
            $user->add_address($addr);

            return true;

        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Delete an address.
     *
     * @param $address Id of relevant address.
     * @return bool Confirmation.
     * @throws Exception MYSQL errors.
     */
    function delete_address($address): bool
    {
        try {
            global $connection;

            $sql = "DELETE FROM `Address` WHERE id = :id";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":id", $address);
            $stmt->execute();

            global $user;

            if ($user == null) {
                $user = $_SESSION["user"];
            }

            $user->refresh_from_db();

            return true;

        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Mark an address as primary.
     * @param $address Id of relevant address.
     * @return bool Confirmation.
     * @throws Exception MYSQL errors.
     */
    function make_primary_address($address): bool
    {
        try {
            global $user;

            if ($user == null) {
                $user = $_SESSION["user"];
            }

            $address_controller = new address_controller();
            $address_controller->set_primary($user, $address);
            $user->refresh_from_db();

            return true;

        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Set a new password for an user.
     *
     * @param string $password New password as string.
     * @return mixed Confirmation.
     * @throws Exception MYSQL errors.
     */
    function set_password(string $password)
    {
        try {
            global $user;
            global $connection;

            if ($user == null) {
                $user = $_SESSION["user"];
            }

            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            $sql = "UPDATE marketplace_db.User t SET t.str_password_hash = :pw WHERE t.id = :user_id;";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":pw", $password_hash);
            $stmt->bindparam(":user_id", $user->id);
            $query = $stmt->execute();
            $user->refresh_from_db();

            return $query;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Get customer who ordered the order.
     *
     * @param Order $order Order id or object.
     * @return User Result.
     */
    function get_user_for_order(Order $order): User
    {
        try {
            //ID was supplied
            if (is_numeric($order)) {
                $id = $order;
            } else {
                $id = $order->id;
            }

            global $connection;
            $sql = "SELECT fk_user FROM `Order` WHERE id = :id";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();
            $query = $stmt->fetch();

            return new User($query["fk_user"]);

        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }

    }

    /**
     * Add an article to the shopping cart of an user.
     *
     * @param int $article_id Id of the article added to the cart.
     * @param int $amount Amount of the article added to the cart.
     * @return mixed True or false.
     */
    public function add_to_shopping_basket(int $article_id, int $amount)
    {
        try {
            $id = $article_id;
            $user = $_SESSION["user"];
            global $connection;

            // Check if given amount is bigger than the available amount
            $sql = "SELECT `int_current_available` FROM `Article` WHERE id = :article_id";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':article_id', $id);
            $stmt->execute();
            $available_amount = $stmt->fetch()['int_current_available'];
            if ($available_amount < $amount) {
                $amount = $available_amount;
            }

            $sql = "Insert into Shopping_Basket (`fk_user`, `fk_article`, `int_amount`) VALUES (:user_id, :article_id, :amount);";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':user_id', $user->id);
            $stmt->bindparam(':article_id', $id);
            $stmt->bindparam(':amount', $amount);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Get the shopping cart for the logged in user.
     *
     * @return array[] with the article id and the amount in the shopping basket.
     */
    public function get_shopping_basket(): array
    {
        try {
            global $connection;

            $user_id = $_SESSION['user']->id;
            $sql = "SELECT a.id, sb.int_amount
                    FROM `Shopping_Basket` sb 
                    JOIN  `Article` a ON a.id = sb.fk_article 
                    WHERE sb.fk_user = :user_id;";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':user_id', $user_id);

            $stmt->execute();
            $results = $stmt->fetchAll();
            $all_articles = array();
            $amount = array();
            foreach ($results as $result) {
                array_push($all_articles, new Article($result['id']));
                array_push($amount, $result['int_amount']);
            }
            return array($all_articles, $amount);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Delete an article from a shopping cart.
     *
     * @param int $article_id The article id.
     * @return mixed True or false.
     */
    public function delete_from_shopping_basket(int $article_id)
    {
        try {
            global $connection;
            $user_id = $_SESSION['user']->id;
            $sql = "DELETE FROM `Shopping_Basket` WHERE fk_user = :user_id AND fk_article = :article_id";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':user_id', $user_id);
            $stmt->bindparam('article_id', $article_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Update the amount of an article in the shopping basket.
     *
     * @param int $article_id The id of an article.
     * @param int $amount The new amount of article.
     * @return mixed True or false.
     */
    public function update_amount_of_article(int $article_id, int $amount)
    {
        try {
            global $connection;
            $user_id = $_SESSION['user']->id;
            $sql = "UPDATE `Shopping_Basket` SET int_amount=:amount WHERE fk_user = :user_id AND fk_article = :article_id";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':user_id', $user_id);
            $stmt->bindparam('article_id', $article_id);
            $stmt->bindparam(':amount', $amount);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Clear the basket of an user.
     *
     * @return mixed True or false.
     */
    public function clear_basket()
    {
        try {
            global $connection;
            $user_id = $_SESSION['user']->id;
            $sql = "DELETE FROM `Shopping_Basket` WHERE fk_user = :user_id";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':user_id', $user_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Get number of articles in the shopping basket.
     *
     * @return array With the status of the SQL-Query and the number of articles in the basket.
     */
    public function get_number_of_articles_in_basket(): array
    {
        try {
            global $connection;
            $user_id = $_SESSION['user']->id;
            $sql = "SELECT COUNT(`fk_article`) FROM `Shopping_Basket` WHERE fk_user = :user_id";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':user_id', $user_id);
            $query = array();
            array_push($query, $stmt->execute());
            array_push($query, $stmt->fetch()[0]);

            return $query;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Calculate sum of shopping basket.
     *
     * @return array With the status of the SQL-Query and the sum of the basket.
     */
    public function calculate_sum_of_basket(): array
    {
        try {
            global $connection;
            $user_id = $_SESSION['user']->id;
            $sql = "SELECT SUM(sb.int_amount * a.float_current_price) 
                    FROM `Shopping_Basket` sb JOIN Article a ON sb.fk_article = a.id WHERE `fk_user` = :user_id";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':user_id', $user_id);
            $query = array();
            array_push($query, $stmt->execute());
            array_push($query, number_format($stmt->fetch()[0], 2, ".", ""));

            return $query;

        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }
}