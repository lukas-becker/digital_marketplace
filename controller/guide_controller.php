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
class guide_controller
{


    /**
     * Get all guides.
     * @param $term
     * @return array
     */
    public function get_all_guides(): array
    {

        try {

            global $connection;

            $sql = "SELECT id FROM Guide";
            $stmt = $connection->prepare($sql);
            $stmt->execute();
            $sql_result = $stmt->fetchAll();
            $guide_ids = [];
            foreach ($sql_result as $guide) {
                array_push($guide_ids, $guide["id"]);
            }
            arsort($guide_ids);

            $guides = [];
            foreach ($guide_ids as $guide) {
                array_push($guides, new Guide($guide));
            }
            return $guides;
        } catch (Exception $e) {
            throw new PDOException($e->getMessage());
        }
    }


}