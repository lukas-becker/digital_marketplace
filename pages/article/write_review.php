<?php
include($_SERVER['DOCUMENT_ROOT'] . "/includes/php_head.inc.php");

$reviewed_Article = new Article($_GET['product_id']);
?>
<!doctype html>
<html lang="en">
<head>
    <?php
    $title = "Marketplace";
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/html_head.inc.php");
    ?>

    <link rel="stylesheet" href="/style/write_review_styles.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
</head>
<body>
<!--Include Navbar-->
<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/navbar.inc.php"); ?>

<div class="container my-5 pt-5">
    <div>
        <h2>Create review</h2>
        <div class="d-flex flex-row">
            <div class="mb-3">
                <img
                        src="data:image;base64,<?php try {
                            echo $reviewed_Article->get_first_image();
                        } catch (Exception $e) {
                        } ?>" class="card-img-top" alt="..."/>
            </div>
            <div class="col pl-1 short-description">
                <p>
                    <?php echo $reviewed_Article->get_title() ?>
                </p>
            </div>
        </div>
    </div>
    <hr/>
    <form method="post" action="confirmation.php">
        <input type="hidden" name="article_id" value="<?php echo $reviewed_Article->get_id() ?>">
        <div>
            <label for="rating" class="form-label">Rating (Requirement)</label>
            <div class="align-baseline">
                <div class="d-flex flex-row">
                    <div class="star-rating" id="rating">
                        <div class="starrating risingstar d-flex justify-content-center flex-row-reverse">
                            <input type="radio" id="star5" name="rating" value="5" required"/><label for="star5"
                                                                                                     title="5 star"></label>
                            <input type="radio" id="star4" name="rating" value="4" required/><label for="star4"
                                                                                                    title="4 star"></label>
                            <input type="radio" id="star3" name="rating" value="3" required/><label for="star3"
                                                                                                    title="3 star"></label>
                            <input type="radio" id="star2" name="rating" value="2" required/><label for="star2"
                                                                                                    title="2 star"></label>
                            <input type="radio" id="star1" name="rating" value="1" required/><label for="star1"
                                                                                                    title="1 star"></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label for="reviewTitle" class="form-label">Add title</label>
            <input type="text" class="form-control" id="reviewTitle" name="reviewTitle"
                   placeholder="What is the most important information? (optional)"/>
        </div>
        <div class="mb-3">
            <label for="reviewDescription" class="form-label">Add a detailed review!</label>
            <textarea class="form-control" id="reviewDescription" rows="5" name="reviewDescription"
                      placeholder="What did you like/dislike? What did you use the product for? (optional)"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send review</button>
    </form>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.inc.php"); ?>


</body>
</html>
