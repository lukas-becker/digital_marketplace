<?php
include $_SERVER['DOCUMENT_ROOT'] . "/includes/php_head.inc.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/Organization.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/controller/article_review_controller.php";
$user = $_SESSION["user"];

if (!isset($_SESSION["login"])) {
    header("Location: /pages/login.php");
    die();
}

if ($_SESSION["user"]->organization == 0) {
    header("Location: /index.php?message=no_org");
    die();
} else {
    $org = new Organization($_SESSION["user"]->organization);
}

function create_star_rating($avg_review)
{
    for ($i = 1; $i <= 5; $i++) {
        $difference = $avg_review - $i;
        if ($difference >= 0) {
            echo '<i class="fas fa-star"></i>';
        } elseif (0.25 < abs($difference) && abs($difference) < 0.75) {
            echo '<i class="fas fa-star-half-alt"></i>';
        } else {
            echo '<i class="far fa-star"></i>';
        }
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <?php
    $title = "Seller Dashboard - Orders";
    include $_SERVER['DOCUMENT_ROOT'] . "/includes/html_head.inc.php";
    ?>

</head>
<body>
<!--Include Navbar-->
<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/navbar.inc.php"); ?>

<div class="container my-5 pt-5">
    <h1>Seller dashboard for <?php echo $org->name; ?></h1>
    <div class="row my-4">
        <!-- Menu -->
        <?php include $_SERVER["DOCUMENT_ROOT"] . "/includes/seller_dashboard_menu.inc.php"; ?>
        <!-- Spacing -->
        <div class="col-md-1"></div>
        <!-- Content -->
        <div class="col-md-8">
            <h3>What customers say about your products</h3>
            <?php
            //New Article Review Controller
            $article_review_controller = new article_review_controller();
            //Get Reviews
            $reviews = $article_review_controller->get_newest_reviews_for_org($org->id);
            if (sizeof($reviews) != 0) {
                foreach ($reviews as $review) {
                    ?>
                    <div class="border-bottom mb-3">
                        <span class="fw-bold">
                            <?php echo $review->get_user() ?> @
                            <a href="/pages/article/detailed_article.php?article=<?php echo $review->get_article()->get_id(); ?>">
                                <?php echo $review->get_article()->get_title(); ?>
                            </a>
                        </span><br>
                        <span class="text-warning"><?php create_star_rating($review->get_rating()) ?></span><br>
                        <h5 class="mt-2"><?php echo $review->get_title() ?></h5>
                        <p><?php echo $review->get_text() ?></p>
                    </div>
                <?php }
            } else { ?>
                <div class="border-bottom mb-3">

                </div>
            <?php } ?>
        </div>


    </div>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.inc.php"); ?>

</body>
</html>
