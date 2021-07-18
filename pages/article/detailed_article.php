<?php
include($_SERVER['DOCUMENT_ROOT'] . "/includes/php_head.inc.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/model/Property.php");
require_once $_SERVER['DOCUMENT_ROOT'] . "/controller/article_review_controller.php";

if (!isset($_GET["article"]) || $_GET["article"] == 0) {
    header("Location: /index.php?message=true");
    die;
}

$article_controller = new article_controller();
$category_controller = new category_controller();
$displayed_article = $article_controller->get_article_by_id($_GET['article']);
$avg_review = $article_controller->get_average_rating($displayed_article);
$ratings = $article_controller->get_count_of_ratings($displayed_article);
$distribution_ratings = $article_controller->get_percentage_of_ratings($displayed_article);
$pictures = $article_controller->get_pictures_of_article($displayed_article);
$highlights = $article_controller->get_highlights_of_article($displayed_article);
$user = $_SESSION["user"];
$available_stock = $displayed_article->get_current_available();

$review_controller = new article_review_controller();
$reviews = $review_controller->get_review_by_article($displayed_article);

if ($displayed_article->auction) {
    if (is_null($user) || ($displayed_article->auction && (!$user->get_is_vip() && !$displayed_article->get_organization() == $user->organization && !$user->is_site_admin()))) {
        header("Location: /index.php?message=not_vip");
        die;
    }

    echo '<script>let auction = true;</script>';
} else {
    echo '<script>let auction = false;</script>';
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

$visited = [];
if (isset($_COOKIE["visited_articles"])) {
    $visited = json_decode($_COOKIE["visited_articles"]);
    $continue = true;
    foreach ($visited as $id) {
        if ($id == $_GET["article"]) {
            $continue = false;
        }
    }
    if ($continue) {
        if (count($visited) == 3) {
            array_pop($visited);
        }
        array_unshift($visited, $_GET["article"]);
    }

} else {
    array_unshift($visited, $_GET["article"]);
}

setcookie(
    "visited_articles",
    json_encode($visited),
    time() + (365 * 24 * 60 * 60),
    "/"
);

?>
<!doctype html>
<html lang="en">
<head>
    <?php
    $title = $displayed_article->get_title() . " - Details";
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/html_head.inc.php");
    ?>
    <link rel="stylesheet" href="/style/detailed_article_styles.css">

    <script>
        function addToShoppingBasket(article_id) {
            Toast();
            applyAmountForToast();

            let amount = document.getElementById("amount").value;

            const xmlhttp = new XMLHttpRequest();

            let parameter = "add_to_shopping_basket=true";
            parameter += "&article_id=" + article_id;
            parameter += "&article_amount=" + amount;

            xmlhttp.open("POST", "/controller/user_controller.php", true);
            xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                }
            };
            xmlhttp.send(parameter);


            let parameter2 = "number_articles_in_basket=true";

            const xmlhttp2 = new XMLHttpRequest();

            xmlhttp2.open("POST", "/controller/user_controller.php", true);
            xmlhttp2.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            xmlhttp2.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    const result = JSON.parse(this.responseText);
                    document.getElementById("number_articles_in_basket").innerHTML = result['result'];
                }
            };
            xmlhttp2.send(parameter2);


        }

        const option = {
            animation: true,
            delay: 2000
        };

        function Toast() {
            const toastHTMLElement = document.getElementById("addToShoppingCartToast");

            const toastElement = new bootstrap.Toast(toastHTMLElement, option);

            toastElement.show()
        }

        function applyAmountForToast() {
            document.getElementById("amount_for_toast").innerHTML = document.getElementById("amount").value;
        }

        if (auction) {
            refreshBid()
        }

        var current_bid = 0;

        function refreshBid() {
            <?php echo 'let id = ' . $_GET["article"] . ";"; ?>
            var xmlhttp = new XMLHttpRequest();

            let parameter = "latest_bid=true";
            parameter += "&article=" + id;

            xmlhttp.open("POST", "/controller/article_controller.php", true);
            xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText)
                    var result = JSON.parse(this.responseText);
                    if (result["status"]) {
                        if (result["time"] != "Auction has ended!") {
                            document.getElementById("bidAmount").innerText = result["amount"] + "€";
                            current_bid = result["amount"];
                            if (result["highestBidder"]) {
                                document.getElementById("highestBidder").classList.remove("d-none")
                            } else {
                                document.getElementById("highestBidder").classList.add("d-none")
                            }
                        }

                    } else {
                        document.getElementById("bidAmount").innerText = "No bids yet";
                    }

                    if (result["time"] == "Auction has ended!") {
                        document.getElementById("bidding").innerText = result["time"];
                        document.getElementById("biddingButton").remove();
                    } else {
                        document.getElementById("remainingTime").innerHTML = result["time"];
                        setTimeout(refreshBid, 1000);
                    }

                }
            };

            xmlhttp.send(parameter);


        }

        function checkBid(caller) {
            if (parseFloat(caller.value) <= parseFloat(current_bid)) {
                caller.value = null;
                document.getElementById("bidError").innerHTML = "Amount to low!<br>";
                document.getElementById("bidError").classList.remove("d-none");
                setTimeout(hideError, 1000)
            }
        }

        function sendBid() {
            let amount = document.getElementById("amount").value;

            if (amount != null) {
                var xmlhttp = new XMLHttpRequest();

                let parameter = "bid=true";
                parameter += "&article=" + <?= $_GET["article"] ?>;
                parameter += "&user=" + <?php echo $user->id; ?>;
                parameter += "&bid=" + amount;

                xmlhttp.open("POST", "/controller/article_controller.php", true);
                xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                xmlhttp.onreadystatechange = function () {
                    if (this.readyState == 4 && this.status == 200) {
                        console.log(this.responseText);
                        var result = JSON.parse(this.responseText);
                        if (result["status"]) {
                            document.getElementById("bidAmount").innerText = amount + "€";
                            document.getElementById("bidSuccess").innerHTML = "Bid successful!<br>";
                            document.getElementById("bidSuccess").classList.remove("d-none");
                            setTimeout(hideSuccess, 5000)
                        } else {
                        }

                    }
                };

                xmlhttp.send(parameter);
            }

        }

        function hideSuccess() {
            document.getElementById("bidSuccess").classList.add("d-none");
        }

        function hideError() {
            document.getElementById("bidError").classList.add("d-none");
        }


    </script>
</head>
<body>
<!--Include Navbar-->
<?php include $_SERVER["DOCUMENT_ROOT"] . "/includes/navbar.inc.php"; ?>
<?php
if ($available_stock == 0) {
    ?>
    <div class="alert alert-danger d-flex justify-content-center mt-5 pt-4" role="alert">
        This article is sold out!
    </div>
    <?php
}
?>
<div class="container my-5 pt-5">
    <div class="row justify-content-center mb-3">

        <div class="col-md-8">
            <!-- Title and reviews -->
            <h2><?php print_r($displayed_article->get_title()) ?></h2>
            <p><a href="#review_header">
                    <span class="text-warning">
                        <?php create_star_rating($avg_review) ?>
                    </span> <?php echo '(' . $article_controller->get_number_of_reviews($displayed_article) . ' Ratings)'; ?>
                </a>
            </p>

            <!-- Images -->
            <div class="ratio-1x1">
                <?php if (sizeof($pictures) > 0) { ?>
                    <div id="carouselExampleIndicators" class="carousel carousel-dark slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <?php for ($i = 0; $i < sizeof($pictures); $i++) { ?>
                                <button type="button" data-bs-target="#carouselExampleIndicators"
                                        data-bs-slide-to="<?php echo $i ?>" <?php if ($i == 0) {
                                    echo 'class="active" aria-current="true"';
                                } ?> aria-label="<?php echo "label " . ($i + 1) ?>"></button>
                            <?php } ?>
                        </div>
                        <div class="carousel-inner rounded">
                            <?php for ($i = 0; $i < sizeof($pictures); $i++) { ?>

                                <div class="carousel-item <?php if ($i == 0) {
                                    echo "active";
                                } ?>"
                                >
                                    <div class="d-flex align-items-stretch justify-content-center">
                                        <img src="<?php echo "data:image;base64," . $pictures[$i]['str_image']; ?>"
                                             class="d-block">
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
                                data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
                                data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- Order Information & Highlights -->
        <div class="col-md-4">
            <div class="card shadow-none mt-5">
                <div class="card-header">Order Information</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        Seller: <?php print_r($article_controller->get_organization_of_article($displayed_article)) ?>
                    </li>
                    <li class="list-group-item">Estimated Shipping Time <span
                                class="fw-bold"><?php print_r($displayed_article->get_days_until_shipping()) ?></span>
                        days
                    </li>
                    <?php
                    if (!$displayed_article->auction) {
                        ?>
                        <li class="list-group-item">Price: <span
                                    class="float-right"><?php echo number_format($displayed_article->get_current_price(), 2, ",", "") ?> €</span>
                        </li>
                    <?php } ?>
                    <li class="list-group-item">Shipping: <span class="float-right">
                            <?php
                            $shipping_cost = $displayed_article->get_shipping_cost();
                            if ($shipping_cost == 0) {
                                echo "Free delivery!";
                            } else {
                                echo $shipping_cost;
                            }
                            ?></span>
                    </li>


                    <?php
                    if (isset($user) && $displayed_article->get_organization() == $user->organization) {
                        if (!$displayed_article->auction) {
                            echo '<a href="/pages/article/edit.php?article=' . $displayed_article->get_id() . '" class="list-group-item list-group-item-action bg-success text-white">Edit</a>';
                        } else {
                            echo '<a href="/pages/article/edit_auction.php?article=' . $displayed_article->get_id() . '" class="list-group-item list-group-item-action bg-success text-white">Edit</a>';
                        }

                    } else {
                        if (!($displayed_article->auction)) { ?>
                            <li class="list-group-item">
                                <form>
                                    <div class="d-flex align-items-center" style="padding: .5rem 1rem;">
                                        <label for="amount" class="form-label align-middle">Amount:</label>
                                        <input type="number" id="amount" class="form-control ms-2" name="quantity"
                                               min="1" max="<?php echo $available_stock ?>"
                                               value="1" onKeyDown="return false">
                                    </div>
                                </form>
                            </li>
                            <a class="list-group-item list-group-item-action bg-success text-white <?php if (!$user || $available_stock == 0) {
                                echo 'disabled';
                            } ?>" onclick="addToShoppingBasket(<?php echo $displayed_article->get_id() ?>)"
                               style="cursor: pointer;">Add to Basket</a>
                            <?php
                        } else { ?>
                            <li class="list-group-item" id="bidding">
                                Current Bid: <span id="bidAmount"></span><br>
                                <span>Your Bid (€):</span>
                                <input type="number" id="amount" class="form-control" name="quantity" step="0.01"
                                       onchange="checkBid(this)">
                                <span id="bidError" class="d-none text-danger"><br></span>
                                <span id="bidSuccess" class="d-none text-success"><br></span>
                                <span id="highestBidder"
                                      class="d-none text-success">You are the highest bidder<br></span>
                                <span id="remainingTime"></span><br>
                            </li>
                            <a class="list-group-item list-group-item-action bg-success text-white rounded-0"
                               id="biddingButton" onclick="sendBid()" style="cursor: pointer;">Bid Now</a>
                            <?php
                        }
                    }

                    ?>

                </ul>
            </div>

            <?php
            if (count($highlights) > 0)
                echo '<h4 class="mt-5">Article Highlights</h4>';
            ?>
            <ul>
                <?php
                foreach ($highlights as $highlight) {
                    echo "<li>" . $highlight['str_highlight'] . "</li>";
                }
                ?>
            </ul>

            <div class="toast" id="addToShoppingCartToast" role="alert" aria-live="assertive" aria-atomic="true"
                 style="position:absolute; bottom: 20px; right: 20px; z-index: 1031;">
                <div class="toast-header">
                    <img src="<?php echo "data:image;base64," . $pictures[0]['str_image']; ?>" class="rounded me-2"
                         style="height: 50px; width: auto;" alt="Image of Article"/>
                    <strong class="me-auto">Article added to cart</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    You have added <?php echo $displayed_article->get_title() ?> to your basket! <br/>
                    Amount: <span id="amount_for_toast"></span>
                </div>
            </div>


        </div>
    </div>

    <!-- Article Description -->
    <div class="row mt-5">

        <div class="col">
            <h3>Description</h3>
            <?php echo $displayed_article->get_description() ?>
        </div>

    </div>

    <!-- Category Properties -->
    <?php
    $categories = $category_controller->get_categories_for_articles($displayed_article);
    $properties = $category_controller->get_property_ids_for_categories($categories);

    if (count($properties) > 0) { ?>
        <div class="row mt-5">

            <div class="col">
                <div class="card shadow-none">
                    <div class="card-header">Detailed Information</div>
                    <ul class="list-group list-group-flush">
                        <?php
                        foreach ($properties as $property_id) {
                            $property = new Property($property_id);
                            ?>
                            <li class="list-group-item"><?php echo $property->getName(); ?>:
                                <?php echo $property->get_value_by_article($displayed_article->get_id()); ?>
                            </li>

                        <?php } ?>


                    </ul>
                </div>
            </div>

        </div>
    <?php } ?>

    <!-- Recommended Products -->
    <div class="row mt-5">
        <h3 class="col">
            Recommended Products
        </h3>
        <div class="w-100"></div>

        <?php
        $a_cont = new article_controller();
        $home_articles = $a_cont->get_random_articles(3);
        foreach ($home_articles as $article) {
            ?>
            <div class="col-md-3 px-md-5">
                <?php include $_SERVER["DOCUMENT_ROOT"] . "/includes/display_helpers/article_card.inc.php"; ?>
            </div>
        <?php } ?>

    </div>

    <!-- Reviews -->
    <h4 class="mt-5" id="review_header">Reviews</h4>
    <div class="row">
        <?php if ($avg_review != 0) { ?>
            <div class="col">
                <div class="card px-2 mb-4">
                    <div class="card-body px-0">
                        Total Rating:
                        <br/> <?php echo round($avg_review, 1) . ' (' . $article_controller->get_number_of_reviews($displayed_article) . ' votes)' ?>
                        <span class="text-warning">
                                    <?php create_star_rating($avg_review) ?>
                                </span><br>

                        <div class="row mx-1 px-0">
                            <div class="progress mt-1 col-8 px-0" data-bs-toggle="tooltip" data-bs-placement="right"
                                 title="<?php echo $ratings['5'] . ' vote(s) (' . $distribution_ratings['5'] . '%)'; ?>">
                                <div class="progress-bar" role="progressbar"
                                     style="width: <?php echo $distribution_ratings['5']; ?>%"
                                     aria-valuenow="<?php echo $distribution_ratings['5']; ?>" aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                            <a class="col-sm" href="#">5 Star</a>
                        </div>
                        <div class="row mx-1">
                            <div class="progress mt-1 col-8 px-0" data-bs-toggle="tooltip" data-bs-placement="right"
                                 title="<?php echo $ratings['4'] . ' vote(s) (' . $distribution_ratings['4'] . '%)'; ?>">
                                <div class="progress-bar" role="progressbar"
                                     style="width: <?php echo $distribution_ratings['4']; ?>%"
                                     aria-valuenow="<?php echo $distribution_ratings['4']; ?>"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <a class="col" href="#">4 Star</a>
                        </div>
                        <div class="row mx-1">
                            <div class="progress mt-1 col-8 px-0" data-bs-toggle="tooltip" data-bs-placement="right"
                                 title="<?php echo $ratings['3'] . ' vote(s) (' . $distribution_ratings['3'] . '%)'; ?>">
                                <div class="progress-bar" role="progressbar"
                                     style="width: <?php echo $distribution_ratings['3']; ?>%"
                                     aria-valuenow="<?php echo $distribution_ratings['3']; ?>"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <a href="#" class="col">3 Star</a>
                        </div>
                        <div class="row mx-1">
                            <div class="progress mt-1 col-8 px-0" data-bs-toggle="tooltip" data-bs-placement="right"
                                 title="<?php echo $ratings['2'] . ' vote(s) (' . $distribution_ratings['2'] . '%)'; ?>">
                                <div class="progress-bar" role="progressbar"
                                     style="width: <?php echo $distribution_ratings['2']; ?>%"
                                     aria-valuenow="<?php echo $distribution_ratings['2']; ?>"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <a href="#" class="col">2 Star</a>
                        </div>
                        <div class="row mx-1">
                            <div class="progress mt-1 col-8 px-0" data-bs-toggle="tooltip" data-bs-placement="right"
                                 title="<?php echo $ratings['1'] . ' vote(s) (' . $distribution_ratings['1'] . '%)'; ?>">
                                <div class="progress-bar" role="progressbar"
                                     style="width: <?php echo $distribution_ratings['1']; ?>%"
                                     aria-valuenow="<?php echo $distribution_ratings['1']; ?>"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <a href="#" class="col">1 Star</a>
                        </div>
                    </div>
                </div>
                <?php if (isset($_SESSION["user"])) { ?>
                    <form action="write_review.php">
                        <input type="hidden" name="product_id" value="<?php echo $displayed_article->get_id() ?>">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-block">Write review!</button>
                        </div>
                    </form>
                <?php } else { ?>
                    <div class="d-grid">
                        <button type="button" class="btn btn-secondary" disabled>Write review!</button>
                    </div>
                    <div class="alert alert-danger" role="alert">
                        You have to be logged in to write reviews!
                    </div>
                <?php } ?>
            </div>

        <?php } else { ?>
            <div class="col card p-3">
                <h5 class="card-title">
                    No Reviews
                </h5>
                <div class="card-body px-0">
                    There are no reviews available yet. Be the first to write a review about this product!
                </div>
                <?php if (isset($_SESSION["user"])) { ?>
                    <form action="write_review.php">
                        <input type="hidden" name="product_id" value="<?php echo $displayed_article->get_id() ?>">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Write review!</button>
                        </div>
                    </form>
                <?php } else { ?>
                    <div class="d-grid">
                        <button type="button" class="btn btn-primary" disabled>Write review!</button>
                    </div>
                    <div class="alert alert-danger" role="alert">
                        You have to be logged in to write reviews!
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <div class="col-1"></div>
        <div class="col-8">
            <?php
            if (sizeof($reviews) != 0) {
                foreach ($reviews as $review) {
                    ?>
                    <div class="border-bottom mb-3">
                        <span class="fw-bold"><?php echo $review->get_user() ?></span><br>
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
