<?php


require_once $_SERVER['DOCUMENT_ROOT'] . "/model/User.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/Article.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/database/connection.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/Guide.php";

if (!isset($connection)) {
    establish_db_connection();
}

/**
 * Class address_controller
 */
class search_controller
{

    public $php_dump;

    /**
     * Search articles for a string.
     *
     * @param string $term Searchstring.
     * @return array Result.
     */
    public function search_by_contained_term($term): array
    {

        try {

            global $connection;
            $term = strtolower($term);
            $sql = "SELECT id FROM Article WHERE LOWER(str_title) like CONCAT('%',:term,'%')";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':term', $term);

            $stmt->execute();
            $article_ids = [];
            $sql_result = $stmt->fetchAll();
            $this->php_dump = $sql_result;
            foreach ($sql_result as $article) {
                array_push($article_ids, $article["id"]);
            }
            arsort($article_ids);

            $articles = [];
            foreach ($article_ids as $article) {
                array_push($articles, new Article($article));
            }
            return $articles;
        } catch (Exception $e) {
            throw new PDOException($e->getMessage());
        }
    }


    /**
     * Search article by guide answers.
     *
     * @param $answers . that were selected in a Guide
     * @return array of articles suitable for the selected answers
     */
    public function search_by_answers($answers)
    {
        $filter = "";
        $flag = false;
        foreach ($answers as $answer) {
            if ($flag) {
                $filter .= " AND ";
            } else $flag = true;
            $curr_answer = new Answer($answer);
            $filter .= $curr_answer->getCondition();
        }
        try {

            global $connection;

            $sql = "SELECT DISTINCT fk_article, float_current_price FROM Article_Property AP join Article A 
                    on AP.fk_article = A.id WHERE " . $filter;
            $stmt = $connection->prepare($sql);
            $stmt->execute();
            $sql_result = $stmt->fetchAll();

            $articles = [];
            foreach ($sql_result as $row) {
                $article = new Article($row["fk_article"]);
                array_push($articles, $article);
            }
            return $articles;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

}