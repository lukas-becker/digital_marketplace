<?php
include($_SERVER['DOCUMENT_ROOT'] . "/includes/php_head.inc.php");

$category_controller = new category_controller();
$categories = $category_controller->get_all_categories();
?>

<!doctype html>
<html lang="en">
<head>
    <?php
    $title = "Categories";
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/html_head.inc.php");
    ?>
    <link href="/style/categories_styles.css" rel="stylesheet">
</head>
<body>
<!--Include Navbar-->
<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/navbar.inc.php"); ?>

<div class="container my-5 pt-5">
    <h1>Categories</h1>
    <div class="row">
        <?php foreach ($categories as $category) {
            $article = $category_controller->get_article_for_category($category);
            ?>
            <div class="col-6 col-md-3">
                <a href="article/all_articles?cat%5B%5D=<?php echo $category->get_id() ?>">
                    <div class="card mb-5">
                        <img src="data:image;base64,<?php echo $article->get_first_image(); ?>"
                             class="card-img-top rounded-1"
                             alt="...">
                        <div class="card-img-overlay card-title text-white p-0 mb-0 rounded-1 position-relatvie border-0">
                            <h5 class="position-absolute top-50 start-50 translate-middle"><?php echo str_replace(" ", "&nbsp;", $category->get_name()); ?></h5>
                        </div>
                    </div>
                </a>
            </div>
        <?php } ?>
    </div>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.inc.php"); ?>

</body>
</html>
