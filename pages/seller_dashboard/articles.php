<?php
include $_SERVER['DOCUMENT_ROOT'] . "/includes/php_head.inc.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/Organization.php";
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
    <div class="row mt-4">
        <!-- Menu -->
        <?php include $_SERVER["DOCUMENT_ROOT"] . "/includes/seller_dashboard_menu.inc.php"; ?>
        <!-- Spacing -->
        <div class="col-md-1"></div>
        <!-- Content -->
        <div class="col-md-8">
            <h3>Your products</h3>
            <hr>
            <?php
            //Initialize Article Controller
            $article_controller = new article_controller();
            //Get Orders for Current User
            $articles = $article_controller->get_articles_for_organization($_SESSION["user"]->organization);
            //loop over orders

            ?>

            <div class="row my-4">
                <?php
                foreach ($articles as $article) {
                    ?>
                    <div class="col-1"></div>
                    <div class="col-10 col-xl-4">
                        <?php include $_SERVER["DOCUMENT_ROOT"] . "/includes/display_helpers/article_card.inc.php"; ?>
                    </div>

                    <div class="col-1"></div>
                <?php } ?>
            </div>
        </div>
    </div>

    <?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.inc.php"); ?>

</body>
</html>
