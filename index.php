<?php
include $_SERVER['DOCUMENT_ROOT'] . "/includes/php_head.inc.php";
?>
<!doctype html>
<html lang="en">
<head>
    <?php
    $title = 'Marketplace';
    include $_SERVER['DOCUMENT_ROOT'] . "/includes/html_head.inc.php";
    ?>
</head>
<body>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/navbar.inc.php"); ?>

<div class="container my-5 pt-5">
    <h1>These products could interest you</h1>
    <div class="row">
        <div class="d-flex flex-wrap mt-4 justify-content-between" id="cardContainer">
            <?php
            $a_cont = new article_controller();
            $home_articles = $a_cont->get_random_articles(3);
            foreach ($home_articles as $article) {
                ?>
                <div class="col-md-3 mx-3">
                    <?php include $_SERVER["DOCUMENT_ROOT"] . "/includes/display_helpers/article_card.inc.php"; ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>


<?php
if (isset($_GET["message"])) {
    include $_SERVER["DOCUMENT_ROOT"] . "/includes/message_modal.inc.php";
}

?>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.inc.php"); ?>
</body>
</html>
