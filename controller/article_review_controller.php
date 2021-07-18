<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/database/connection.php");
if (!isset($connection)) {
    establish_db_connection();
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/model/ArticleReview.php");

class article_review_controller
{

    private $all_reviews = [];

    /**
     * Retrieves all reviews from the database
     *
     * @return array of ArticleReview with all reviews
     */
    public function get_all_reviews(): array
    {
        if ($this->all_reviews == []) {
            global $connection;

            $sql = "SELECT id FROM `Article_Review`";
            $query = $connection->query($sql);

            $review_list = [];

            foreach ($query->fetchAll() as $review) {
                $review = new ArticleReview($review['id']);
                //$category->get_by_id($sql_result['id']);
                array_push($review_list, $review);
            }
            $this->all_reviews = $review_list;
        }
        return $this->all_reviews;
    }

    /**
     * Get all written reviews for an article
     *
     * @param Article $article the article used for the reviews
     * @return array of ArticleReview with the reviews for the given article
     */
    public function get_review_by_article(Article $article): array
    {
        try {
            global $connection;

            $article_id = $article->get_id();
            $sql = "SELECT id FROM `Article_Review` WHERE fk_article = :article";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(":article", $article_id);
            $stmt->execute();

            $result = $stmt->fetchAll();

            $reviews_for_article = array();
            foreach ($result as $review) {
                $temp = new ArticleReview($review['id']);
                array_push($reviews_for_article, $temp);
            }
            return $reviews_for_article;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Add a new ArticleReview to an article
     *
     * @param string $title Title of review
     * @param string $description Description of review
     * @param float $rating Star rating given to the article
     * @param int $user_id The user who has written the review
     * @param int $article_id The article for which the review was written
     * @return bool True if the SQL-Statement was successful
     */
    public function create_review_for_article(string $title, string $description, float $rating, int $user_id, int $article_id): bool
    {
        try {
            global $connection;

            $sql = "INSERT INTO `Article_Review` (`str_title`, `str_text`, `float_rating`, `fk_article`, `fk_user`) VALUES (:title, :text, :rating, :article, :user_id)";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':title', $title);
            $stmt->bindparam(':text', $description);
            $stmt->bindparam('rating', $rating);
            $stmt->bindparam(':article', $article_id);
            $stmt->bindparam(':user_id', $user_id);
            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get the latest reviews for an organization's articles
     *
     * @param int $org whether an Organization-Object or an Id
     * @return array of ArticleReview of the first 10 reviews
     */
    public function get_newest_reviews_for_org(int $org): array
    {
        $reviews = array();

        $id = $org;

        global $connection;

        $sql = "SELECT ar.id FROM `Article_Review` ar join `Article` a on (a.id = ar.fk_article) WHERE a.fk_organization = :org_id ORDER BY id DESC LIMIT 10";
        $stmt = $connection->prepare($sql);
        $stmt->bindparam(':org_id', $id);
        $stmt->execute();

        foreach ($stmt->fetchAll() as $review) {
            $reviewObject = new ArticleReview($review["id"]);
            array_push($reviews, $reviewObject);
        }
        return $reviews;
    }
}