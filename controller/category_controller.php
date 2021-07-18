<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/database/connection.php");
if (!isset($connection)) {
    establish_db_connection();
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/model/Category.php");

class category_controller
{
    private $all_categories = [];

    /**
     * Returns all categories.
     *
     * @return array List of categories.
     */
    public function get_all_categories(): array
    {
        if ($this->all_categories == []) {
            global $connection;

            $sql = "SELECT id from Category;";
            $query = $connection->query($sql);

            $categoryList = [];

            foreach ($query->fetchAll() as $category) {
                $category = new Category($category['id']);
                array_push($categoryList, $category);
            }

            $this->all_categories = $categoryList;
        }

        return $this->all_categories;
    }

    /**
     * Returns all categories for an article.
     *
     * @param $article . Concerning article.
     * @return array List of categories.
     */
    public function get_categories_for_articles($article): array
    {
        global $connection;

        if (is_numeric($article)) {
            $id = $article;
        } else {
            $id = $article->get_id();
        }


        $sql = "SELECT fk_category FROM Article_Category WHERE fk_article = :id";
        $stmt = $connection->prepare($sql);
        $stmt->bindparam(':id', $id);
        $stmt->execute();
        $cat_list = [];

        foreach ($stmt->fetchAll() as $cat) {
            array_push($cat_list, $cat['fk_category']);
        }
        return $cat_list;

    }

    /**
     * Return random article from category.
     *
     * @param Category $category Concerning category.
     * @return Article
     */
    public function get_article_for_category(Category $category): Article
    {
        global $connection;

        $id = 0;

        //ID was supplied
        if (is_numeric($category)) {
            $id = $category;
        } else {
            $id = $category->get_id();
        }

        $sql = "SELECT fk_article FROM Article_Category WHERE fk_category = :cat_id ORDER BY RAND() LIMIT 1";
        $stmt = $connection->prepare($sql);
        $stmt->bindparam(':cat_id', $id);
        $stmt->execute();

        foreach ($stmt->fetchAll() as $current_article) {
            $article = new Article($current_article['fk_article']);
            if (isset($article)) return $article;
        }
    }

    /**
     * Check if an article is in a list of categories.
     *
     * @param Article $article Concerning article.
     * @param array $categories List of categories to check.
     * @return bool True or false.
     */
    public function is_in_categories(Article $article, array $categories): bool
    {
        try {
            global $connection;
            $id = $article->get_id();


            $sql = "SELECT fk_category FROM Article_Category WHERE fk_article = :id;";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();
            $owned_categories = [];
            foreach ($stmt->fetchAll() as $current) {
                $cat_id = $current['fk_category'];
                array_push($owned_categories, $cat_id);
            }
            foreach ($categories as $cat) {
                if (!in_array($cat, $owned_categories)) return false;
            }
            return true;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }


    }

    /**
     * Check if an article is in a category.
     *
     * @param Article $article Concerning article.
     * @param int $category Category to check.
     * @return bool True or false.
     */
    public function is_in_category(Article $article, int $category): bool
    {
        try {
            global $connection;
            $id = $article->get_id();


            $sql = "SELECT fk_category FROM Article_Category WHERE fk_article = :id;";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':id', $id);
            $stmt->execute();
            $owned_categories = [];
            foreach ($stmt->fetchAll() as $current) {
                $cat_id = $current['fk_category'];
                array_push($owned_categories, $cat_id);
            }
            if (!in_array($category, $owned_categories)) return false;

            return true;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }


    }

    /**
     * Get properties connected to a category
     *
     * @param int $category Concerning category.
     * @return array List of properties.
     */
    public function get_property_ids_for_category(int $category): array
    {
        try {
            global $connection;


            $sql = "SELECT fk_property FROM Category_Property WHERE fk_category = :category";
            $stmt = $connection->prepare($sql);
            $stmt->bindparam(':category', $category);
            $stmt->execute();
            $owned_properties = [];
            foreach ($stmt->fetchAll() as $current) {
                $prop_id = $current['fk_property'];
                array_push($owned_properties, $prop_id);
            }
            return $owned_properties;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Get properties connected to multiple categories
     *
     * @param array $categories Concerning categories.
     * @return array List of properties.
     */
    public function get_property_ids_for_categories(array $categories): array
    {
        $owned_properties = [];
        foreach ($categories as $category) {
            $curr_props = $this->get_property_ids_for_category($category);
            $owned_properties = array_unique(array_merge($owned_properties, $curr_props), SORT_REGULAR);
        }
        return $owned_properties;
    }
}