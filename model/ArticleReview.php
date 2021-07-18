<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/database/connection.php");

if (!isset($connection)) {
    establish_db_connection();
}

class ArticleReview
{
    /**
     * All attributes of an address
     */
    private $id;
    private $title;
    private $text;
    private $rating;
    private $article;
    private $user;

    /**
     * ArticleReview constructor.
     * @param $id
     */
    public function __construct($id)
    {
        $this->get_by_id($id);
    }

    /**
     * Get the data for an article review object from the database
     *
     * @param int $id Id of an article review
     */
    public function get_by_id(int $id)
    {
        try {
            global $connection;
            $sql = "SELECT * FROM `Article_Review` WHERE id = :id";

            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();

            $sql_result = $stmt->fetch();

            $this->id = $id;
            $this->title = $sql_result['str_title'];
            $this->text = $sql_result['str_text'];
            $this->rating = $sql_result['float_rating'];
            $this->article = new Article($sql_result['fk_article']);
            $this->user = new User($sql_result['fk_user']);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Returns the article id for which the review was written
     *
     * @return mixed article id
     */
    public function get_article()
    {
        return $this->article;
    }

    /**
     * Returns the id of the article review
     *
     * @return mixed id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Returns the title of the article review
     *
     * @return mixed title
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * Returns the text of the article review
     *
     * @return mixed text
     */
    public function get_text()
    {
        return $this->text;
    }

    /**
     * Returns the rating of the article review
     *
     * @return mixed rating
     */
    public function get_rating()
    {
        return $this->rating;
    }

    /**
     * Returns the user (id) who wrote the article
     *
     * @return mixed user id
     */
    public function get_user()
    {
        if ($this->user->public_name == null) {
            return $this->user->first_name;
        }
        return $this->user->public_name;
    }
}