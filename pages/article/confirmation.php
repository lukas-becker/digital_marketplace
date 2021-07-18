<?php
include($_SERVER['DOCUMENT_ROOT'] . "/includes/php_head.inc.php");


$article_review_controller = new article_review_controller();
$success_insert = $article_review_controller->create_review_for_article($_POST['reviewTitle'], $_POST['reviewDescription'], $_POST['rating'], $_SESSION['user']->id, $_POST['article_id']);

?>

<!doctype html>
<html lang="en">
<head>
    <?php
    $title = "Marketplace";
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/html_head.inc.php");
    ?>
</head>
<body>
<!--Include Navbar-->
<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/navbar.inc.php"); ?>

<div class="container my-5 pt-5 text-center">
    <?php if ($success_insert) { ?>
        <h1 class="mb-3">Thank you for your review!</h1>
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="green" class="bi bi-check-circle-fill"
             viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
        </svg>
        <p>You're review has been saved successfully!</p>
    <?php } else { ?>

    <?php } ?>

</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.inc.php"); ?>


</body>
</html>

