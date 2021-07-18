<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/database/connection.php");

// Check if a connection already exist
if (!isset($connection)) {
    establish_db_connection();
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/model/Article.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/model/User.php");

//This is needed fpr the XMLHTTP Requests
session_start();

/**
 * If this file is called with a GET-Request check if specific attributes are set
 */

/**
 * Create a new article and reroute the user to this article.
 */
if (isset($_GET["create_article"])) {
    $article_controller = new article_controller();
    $id = $article_controller->create_article($_POST["title"], $_POST["price"], $_POST["shipping"], $_POST["location"], $_POST["stock"], $_POST["description"], $_SESSION["user"]->organization, $_POST["highlights"], $_POST["img_txt"]);

    if ($id == 0) {
        header("Location: /index.php?message=art_error");
        die();
    }

    $article_controller->categorize_article($id, $_POST["cat"]);

    header("Location: /pages/article/detailed_article?article=" . $id);
    die();
}

/**
 * Update an existing article and reroute the user to this article.
 */
if (isset($_GET["update_article"])) {
    $id = $_GET["update_article"];
    $article_controller = new article_controller();
    $article_controller->update_article($id, $_POST["title"], $_POST["price"], $_POST["shipping"], $_POST["location"], $_POST["stock"], $_POST["description"], $_POST["highlights"], $_POST["img_txt"]);
    $article_controller->recategorize_article($id, $_POST["cat"]);
    $article_controller->update_article_properties($id, $_POST);
    header("Location: /pages/article/detailed_article?article=" . $id);
    die();
}

/**
 * Create a new auction and reroute the user to this article.
 */
if (isset($_GET["create_auction"])) {
    $article_controller = new article_controller();
    $id = $article_controller->create_auction($_POST["title"], $_POST["shipping"], $_POST["location"], $_POST["auctionEnd"], $_POST["description"], $_SESSION["user"]->organization, $_POST["highlights"], $_POST["img_txt"]);

    if ($id == 0) {
        header("Location: /index.php?message=art_error");
        die();
    }

    $article_controller->categorize_article($id, $_POST["cat"]);
    header("Location: /pages/article/detailed_article?article=" . $id);
    die();
}

/**
 * Update an existing auction and reroute the user to this article.
 */
if (isset($_GET["update_auction"])) {
    $id = $_GET["update_auction"];
    $article_controller = new article_controller();
    $article_controller->update_auction($id, $_POST["title"], $_POST["shipping"], $_POST["location"], $_POST["description"], $_POST["highlights"], $_POST["img_txt"]);
    $article_controller->recategorize_article($id, $_POST["cat"]);
    $article_controller->update_article_properties($id, $_POST);
    header("Location: /pages/article/detailed_article?article=" . $id);
    die();
}

/**
 * Querys the current bid, remaining time and wether the user is the highest bidder.
 */
if (isset($_REQUEST["latest_bid"])) {
    $id = $_REQUEST["article"];
    $article_controller = new article_controller();
    $amount = $article_controller->get_latest_bid($id);
    $time = $article_controller->get_remaining_time($id);
    $highestBidder = $article_controller->is_user_highest_bidder($id);

    if ($amount) {
        $resultArray = ["status" => true, "amount" => $amount, "time" => $time, "highestBidder" => $highestBidder];
    } else {
        $resultArray = ["status" => false, "time" => $time];
    }
    echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
}

/**
 * Sends a bid.
 */
if (isset($_REQUEST["bid"])) {
    $article = $_REQUEST["article"];
    $user = $_REQUEST["user"];
    $bid = $_REQUEST["bid"];
    $article_controller = new article_controller();
    if ($article_controller->send_bid($article, $user, $bid)) {
        $resultArray = ["status" => true];
    } else {
        $resultArray = ["status" => false];
    }
    echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
}

/**
 * Toggles visibility.
 */
if (isset($_REQUEST["set_visibility"])) {
    $article = $_REQUEST["id"];
    $article_controller = new article_controller();
    $res = false;

    if ($_REQUEST["set_visibility"] == "true") {
        $res = $article_controller->show_article($article);
    } else {
        $res = $article_controller->hide_article($article);
    }
    if ($res) {
        $resultArray = ["status" => true];
    } else {
        $resultArray = ["status" => false];
    }
    echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
}

class article_controller
{

    /**
     * Get all articles registered in the database
     *
     * @return array Array of all articles
     */
    public function get_all_articles(): array
    {

        global $connection;

        $sql = "SELECT id FROM Article;";
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $article_list = [];

        foreach ($stmt->fetchAll() as $current_article) {
            //print_r($article);
            $article = new Article($current_article['id']);
            //$category->get_by_id($sql_result['id']);
            array_push($article_list, $article);
        }
        $this->all_articles = $article_list;

        return $this->all_articles;
    }

    /**
     * Get all articles or an organization
     *
     * @param int $org ID of the organization
     * @return array Array of articles
     */
    public function get_articles_for_organization(int $org): array
    {
        global $connection;

        //ID was supplied
        $id = $org;

        $sql = "SELECT id FROM Article WHERE fk_organization = :org_id;";
        $stmt = $connection->prepare($sql);
        $stmt->bindparam(':org_id', $id);
        $stmt->execute();
        $article_list = [];

        foreach ($stmt->fetchAll() as $current_article) {
            //print_r($article);
            $article = new Article($current_article['id']);
            //$category->get_by_id($sql_result['id']);
            array_push($article_list, $article);
        }
        return $article_list;

    }

    /**
     * Get an article by ID
     *
     * @param int $id ID of an article
     * @return Article The article suitable for ID
     */
    public function get_article_by_id(int $id): Article
    {
        return new Article($id);
    }

    /**
     * Returns random articles for display.
     *
     * @param int $amount How many articles should be returned.
     * @return array Array of articles.
     */
    public function get_random_articles(int $amount): array
    {
        global $connection;

        $sql = "SELECT id FROM Article WHERE bool_visible = 1 ORDER BY RAND() LIMIT " . $amount . " ;";
        $stmt = $connection->prepare($sql);
        $stmt->execute();

        $article_list = [];

        foreach ($stmt->fetchAll() as $current_article) {
            //print_r($article);
            $article = new Article($current_article['id']);
            //$category->get_by_id($sql_result['id']);
            array_push($article_list, $article);
        }
        return $article_list;
    }

    /**
     * Get the organization name of an article
     *
     * @param Article $article Article whose organization is read out
     * @return mixed Name of organization
     */
    public function get_organization_of_article(Article $article)
    {
        try {
            $fk_organization = $article->get_organization();
            global $connection;

            $sql = "SELECT `str_name` FROM Organization WHERE id = :fk_organization;";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':fk_organization', $fk_organization);
            $stmt->execute();

            $query = $stmt->fetch();
            return $query['str_name'];
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Calculate average rating of an article
     *
     * @param Article $article Article whose average rating should be calculated
     * @return mixed average rating of the article
     */
    public function get_average_rating(Article $article)
    {
        try {
            $article_id = $article->get_id();
            global $connection;

            $sql = "SELECT AVG(`float_rating`) average_rating FROM `Article_Review` WHERE `fk_article` = :article_id";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':article_id', $article_id);
            $stmt->execute();

            $query = $stmt->fetch();
            return $query['average_rating'];
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Calculates the distribution of percentages among the 5 stars
     *
     * @param Article $article Article for calculation
     * @return int[] Array with 5 entries of percentages
     */
    public function get_percentage_of_ratings(Article $article): array
    {
        $distribution = $this->get_count_of_ratings($article);
        $total_reviews = $this->get_number_of_reviews($article);
        if ($total_reviews == 0) {
            return $distribution;
        } else {
            for ($i = 1; $i <= 5; $i++) {
                $distribution[$i] = round(($distribution[$i] / $total_reviews) * 100, 2);
            }
        }
        return $distribution;
    }

    /**
     * Count for each rating (1-5) of an article the reviews
     *
     * @param Article $article Article for Reviews
     * @return int[] Number of reviews for each rating
     */
    public function get_count_of_ratings(Article $article): array
    {
        try {
            $article_id = $article->get_id();
            global $connection;

            $sql = "SELECT `float_rating`, COUNT(*) number FROM `Article_Review` WHERE `fk_article` = :article_id GROUP BY `float_rating`";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam('article_id', $article_id);
            $stmt->execute();

            $query = $stmt->fetchAll();

            $result = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);
            if (sizeof($query) > 0) {
                foreach ($query as $row) {
                    $result[$row['float_rating']] = $row['number'];
                }
            }
            return $result;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Get the number of all reviews of an article
     *
     * @param Article $article Article to count the reviews
     * @return mixed Number of reviews
     */
    public function get_number_of_reviews(Article $article)
    {
        try {
            $article_id = $article->get_id();
            global $connection;

            $sql = "SELECT Count(*) FROM `Article_Review` WHERE `fk_article` = $article_id";
            $query = $connection->query($sql);
            return $query->fetch()[0];
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Get all pictures of an article
     *
     * @param Article $article Article for accessing pictures
     * @return mixed Array of pictures (base 64)
     */
    public function get_pictures_of_article(Article $article)
    {
        try {
            global $connection;

            $article_id = $article->get_id();

            $sql = "SELECT `str_image` FROM `Article_Images` WHERE `fk_article` = :article_id";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":article_id", $article_id);
            $stmt->execute();

            return $stmt->fetchAll();

        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Get highlights for an article
     *
     * @param Article $article Article for accessing highlights
     * @return mixed Array of highlights
     */
    public function get_highlights_of_article(Article $article)
    {
        try {
            global $connection;

            $article_id = $article->get_id();
            $sql = "SELECT `str_highlight` FROM `Article_Highlight` WHERE `fk_article` = :article_id";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':article_id', $article_id);
            $stmt->execute();

            return $stmt->fetchAll();

        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Sets new properties for an article.
     *
     * @param int $article The relevant article.
     * @param array $post Renaming for the GLOBALS variable $_POST
     */
    public function update_article_properties(int $article, array $post)
    {
        $category_controller = new category_controller();
        $categories = $category_controller->get_categories_for_articles($article);
        $owned_properties = $category_controller->get_property_ids_for_categories($categories);
        $properties = [];
        print_r($owned_properties);
        foreach ($owned_properties as $property) {
            $properties[$property] = $post["property" . $property];
        }
        print_r($properties);

        try {
            global $connection;
            $sql = "DELETE FROM Article_Property WHERE fk_article = :article";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":article", $article);
            $stmt->execute();

            foreach ($owned_properties as $property) {
                $sql = "INSERT INTO `Article_Property` ( fk_article, fk_property, str_value)
                                VALUES (:article, :property, :val)";
                $stmt = $connection->prepare($sql);
                $stmt->bindparam(":article", $article);
                $stmt->bindparam(":property", $property);
                $stmt->bindparam(":val", $properties[$property]);
                $stmt->execute();
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            return;
        }
    }

    /**
     * Store category for an article.
     *
     * @param int $article_id Relevant article.
     * @param $categories List of categories.
     */
    public function categorize_article(int $article_id, $categories)
    {
        if (!isset($categories)) return;
        try {
            global $connection;


            foreach ($categories as $category_id) {
                $sql = "INSERT INTO `Article_Category` ( fk_article, fk_category)
                                VALUES (:article, :category);";
                $stmt = $connection->prepare($sql);
                $stmt->bindparam(":article", $article_id);
                $stmt->bindparam(":category", $category_id);
                $stmt->execute();
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            return;
        }
    }

    /**
     * Change categories for an article.
     *
     * @param int $article_id Relevant article.
     * @param $categories List of categories.
     */
    public function recategorize_article(int $article_id, $categories)
    {
        if (!isset($categories)) return;
        try {
            global $connection;


            $sql = "DELETE FROM Article_Category WHERE fk_article = :article";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":article", $article_id);
            $stmt->execute();

            $this->categorize_article($article_id, $categories);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return;
        }
    }

    /**
     * Create a new article in the database
     *
     * @param string $title Title of the article.
     * @param string $price Price of the article.
     * @param string $shipping_time Shipping time of the article.
     * @param string $location Place of dispatch of the article.
     * @param string $current_available Amount of the article.
     * @param string $description Description of the article.
     * @param int $org Organisation selling the article.
     * @param $highlights Highlights of the article.
     * @param array $images Images of the article.
     * @return int|mixed True or false
     */
    public function create_article(string $title, string $price, string $shipping_time, string $location, string $current_available, string $description, int $org, $highlights, array $images)
    {

        try {
            global $connection;

            $sql = "INSERT INTO `Article` (str_title, str_description, str_location, int_days_until_shipping,
                                        int_current_available, float_current_price, fk_organization, float_shipping_cost)
                                VALUES (:title, :description, :location, :shipping_time, :current_available, :current_price, :org, 0);";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":title", $title);
            $stmt->bindparam(":description", $description);
            $stmt->bindparam(":location", $location);
            $stmt->bindparam(":shipping_time", $shipping_time);
            $stmt->bindparam(":current_available", $current_available);
            $stmt->bindparam(":current_price", $price);
            $stmt->bindparam(":org", $org);

            $stmt->execute();

            $sql = "SELECT id from `Article` ORDER BY id DESC LIMIT 1";
            $stmt = $connection->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetch();
            $id = $result["id"];

            for ($i = 0; $i < count($highlights); $i++) {
                $sql = "INSERT INTO Article_Highlight (fk_article, str_highlight) VALUES (:article, :highlight);";
                $stmt = $connection->prepare($sql);
                $stmt->bindparam(":article", $id);
                $stmt->bindparam(":highlight", $highlights[$i]);
                $stmt->execute();
            }

            for ($i = 1; $i < count($images); $i++) {
                $sql = "INSERT INTO Article_Images (fk_article, str_image) VALUES (:article, :image);";
                $stmt = $connection->prepare($sql);
                $stmt->bindparam(":article", $id);
                $stmt->bindparam(":image", $images[$i]);
                $stmt->execute();
            }

            return $id;

        } catch (PDOException $e) {
            echo $e->getMessage();
            return 0;
        }
    }

    /**
     * Update values saved in the database for an article
     *
     * @param int $id Article id
     * @param string $title Title of the article
     * @param string $price Price of the article
     * @param string $shipping_time Estimated shipping time for the article
     * @param string $location The location from which the article is sent
     * @param string $current_available The available amount of the article
     * @param string $description The description of the article
     * @param $highlights A list of highlights of the article
     * @param array $images A list of images
     * @return int is 0 if SQL-statement fails
     */
    public function update_article(int $id, string $title, string $price, string $shipping_time, string $location, string $current_available, string $description, $highlights, array $images): int
    {

        try {
            global $connection;

            $sql = "UPDATE `Article` SET str_title               = :title,
                                         str_description         = :description,
                                         str_location            = :location,
                                         int_days_until_shipping = :shipping_time,
                                         int_current_available   = :current_available,
                                         float_current_price     = :current_price
                                     WHERE id = :id;";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":title", $title);
            $stmt->bindparam(":description", $description);
            $stmt->bindparam(":location", $location);
            $stmt->bindparam(":shipping_time", $shipping_time);
            $stmt->bindparam(":current_available", $current_available);
            $stmt->bindparam(":current_price", $price);
            $stmt->bindparam(":id", $id);

            $stmt->execute();


            $sql = "DELETE FROM Article_Highlight WHERE fk_article = :id";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":id", $id);
            $stmt->execute();

            for ($i = 0; $i < count($highlights); $i++) {
                $sql = "INSERT INTO Article_Highlight (fk_article, str_highlight) VALUES (:article, :highlight);";
                $stmt = $connection->prepare($sql);
                $stmt->bindparam(":article", $id);
                $stmt->bindparam(":highlight", $highlights[$i]);
                $stmt->execute();
            }

            $sql = "DELETE FROM Article_Images WHERE fk_article = :id";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":id", $id);
            $stmt->execute();

            for ($i = 1; $i < count($images); $i++) {
                $sql = "INSERT INTO Article_Images (fk_article, str_image) VALUES (:article, :image);";
                $stmt = $connection->prepare($sql);
                $stmt->bindparam(":article", $id);
                $stmt->bindparam(":image", $images[$i]);
                $stmt->execute();
            }
            return 1;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return 0;
        }
    }

    /**
     * Decreases the stock of an article by a specific number.
     *
     * @param Article $article The article to decrease the amount.
     * @param int $count The number for decreasing the stock.
     * @return mixed Boolean
     */
    public function decrease_available_articles(Article $article, int $count)
    {
        try {

            global $connection;
            $article_id = $article->get_id();
            $sql = "UPDATE Article SET int_current_available = int_current_available - :count_article WHERE id = :article_id";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":article_id", $article_id);
            $stmt->bindparam(":count_article", $count);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Create a new auction.
     *
     * @param string $title Article name.
     * @param string $shipping_time Duration of shipping.
     * @param string $location Where the article is located.
     * @param string $auction_end Datetime the auction ends.
     * @param string $description Article description.
     * @param int $org The selling organization. (For now only the site administrators)
     * @param array $highlights Article highlights.
     * @param array $images Article images.
     * @return int|mixed Auction ID
     */
    public function create_auction(string $title, string $shipping_time, string $location, string $auction_end, string $description, int $org, array $highlights, array $images)
    {

        try {
            global $connection;

            $sql = "INSERT INTO `Article` (str_title, str_description, str_location, date_auction_end, int_days_until_shipping,
                                        int_current_available, fk_organization, float_shipping_cost, bool_auction)
                                VALUES (:title, :description, :location, :auction_end, :shipping_time, 1, :org, 0, 1);";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":title", $title);
            $stmt->bindparam(":description", $description);
            $stmt->bindparam(":location", $location);
            $stmt->bindparam(":auction_end", $auction_end);
            $stmt->bindparam(":shipping_time", $shipping_time);
            $stmt->bindparam(":org", $org);

            $stmt->execute();

            $sql = "SELECT id from `Article` ORDER BY id DESC LIMIT 1";
            $stmt = $connection->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetch();
            $id = $result["id"];

            for ($i = 0; $i < count($highlights); $i++) {
                $sql = "INSERT INTO Article_Highlight (fk_article, str_highlight) VALUES (:article, :highlight);";
                $stmt = $connection->prepare($sql);
                $stmt->bindparam(":article", $id);
                $stmt->bindparam(":highlight", $highlights[$i]);
                $stmt->execute();
            }

            for ($i = 1; $i < count($images); $i++) {
                $sql = "INSERT INTO Article_Images (fk_article, str_image) VALUES (:article, :image);";
                $stmt = $connection->prepare($sql);
                $stmt->bindparam(":article", $id);
                $stmt->bindparam(":image", $images[$i]);
                $stmt->execute();
            }

            return $id;

        } catch (PDOException $e) {
            echo $e->getMessage();
            return 0;
        }
    }

    /**
     * Update an existing auction.
     *
     * @param int $id Id of the Auction to be updated.
     * @param string $title New article name.
     * @param string $shipping_time New shipping duration.
     * @param string $location New location.
     * @param string $description New description.
     * @param array $highlights New highlights.
     * @param array $images New images (and sorting)
     * @return int Auction Id
     */
    public function update_auction(int $id, string $title, string $shipping_time, string $location, string $description, array $highlights, array $images): int
    {

        try {
            global $connection;

            $sql = "UPDATE `Article` SET str_title               = :title,
                                         str_description         = :description,
                                         str_location            = :location,
                                         int_days_until_shipping = :shipping_time
                                     WHERE id = :id;";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":title", $title);
            $stmt->bindparam(":description", $description);
            $stmt->bindparam(":location", $location);
            $stmt->bindparam(":shipping_time", $shipping_time);
            $stmt->bindparam(":id", $id);

            $stmt->execute();


            $sql = "DELETE FROM Article_Highlight WHERE fk_article = :id";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":id", $id);
            $stmt->execute();

            for ($i = 0; $i < count($highlights); $i++) {
                $sql = "INSERT INTO Article_Highlight (fk_article, str_highlight) VALUES (:article, :highlight);";
                $stmt = $connection->prepare($sql);
                $stmt->bindparam(":article", $id);
                $stmt->bindparam(":highlight", $highlights[$i]);
                $stmt->execute();
            }

            $sql = "DELETE FROM Article_Images WHERE fk_article = :id";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":id", $id);
            $stmt->execute();

            for ($i = 1; $i < count($images); $i++) {
                $sql = "INSERT INTO Article_Images (fk_article, str_image) VALUES (:article, :image);";
                $stmt = $connection->prepare($sql);
                $stmt->bindparam(":article", $id);
                $stmt->bindparam(":image", $images[$i]);
                $stmt->execute();
            }

        } catch (PDOException $e) {
            echo $e->getMessage();
            return 0;
        }
    }

    /**
     * Return the highest bid for an article.
     *
     * @param int $article Concerning auction.
     * @return false|mixed Error or value.
     */
    public function get_latest_bid(int $article)
    {
        $auction_article = new Article($article);
        return $auction_article->get_latest_bid();
    }

    /**
     * Return whether the user is the higest bidder.
     *
     * @param int $article Concerning auction.
     * @return bool True or false.
     */
    public function is_user_highest_bidder(int $article): bool
    {
        $auction_article = new Article($article);
        return $_SESSION["user"]->get_id() == $auction_article->get_highest_bidder();
    }

    /**
     * Returns the remaining time for the auction.
     *
     * @param int $article Concerning auction.
     * @return string Datetime string.
     */
    public function get_remaining_time(int $article): string
    {
        $auction_article = new Article($article);
        return $auction_article->get_remaining_auction_time();
    }

    /**
     * Saves a users bid.
     *
     * @param int $article Concerning auction.
     * @param int $user Bidding user id.
     * @param $bid Amount.
     * @return mixed True or false.
     */
    public function send_bid(int $article, int $user, $bid)
    {
        global $connection;

        $sql = "INSERT INTO marketplace_db.Bids (fk_article, fk_user, float_amount) VALUES (:article, :user_id, :amount)";
        $stmt = $connection->prepare($sql);
        $stmt->bindparam(":article", $article);
        $stmt->bindparam(":user_id", $user);
        $stmt->bindparam(":amount", $bid);

        return $stmt->execute();

    }

    /**
     * Make an article only visible for the owner.
     *
     * @param int $article Concerning auction.
     * @return mixed Confirmation.
     */
    public function hide_article(int $article)
    {
        global $connection;

        $sql = "UPDATE `Article` SET bool_visible = 0 WHERE id = :id;";
        $stmt = $connection->prepare($sql);
        $stmt->bindparam(":id", $article);

        return $stmt->execute();
    }

    /**
     * Make an article visible for everyone.
     *
     * @param int $article Concerning auction.
     * @return mixed Confirmation.
     */
    public function show_article(int $article)
    {
        global $connection;

        $sql = "UPDATE `Article` SET bool_visible = 1 WHERE id = :id;";
        $stmt = $connection->prepare($sql);
        $stmt->bindparam(":id", $article);

        return $stmt->execute();
    }
}