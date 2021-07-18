<?php
include $_SERVER['DOCUMENT_ROOT'] . "/includes/php_head.inc.php";
$user_controller = new user_controller();
$articles = $user_controller->get_shopping_basket()[0];
$amount = $user_controller->get_shopping_basket()[1];
$organizations_for_orders = array();
$organization_article_amount = array();

$article_controller = new article_controller();
?>
<!doctype html>
<html lang="en">
<head>
    <?php
    $title = "Shopping Basket";
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/html_head.inc.php");
    ?>

    <script src="../scripts/shoppingBasket.inc.js">

    </script>
</head>
<body>
<!--Include Navbar-->
<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/navbar.inc.php"); ?>

<div class="container my-5 pt-5">
    <h1>Shopping Basket from <?php echo $_SESSION["user"]->first_name ?></h1>
    <?php
    $sum = 0;
    for ($count = 0; $count < sizeof($articles); $count++) {
        $current_article = $articles[$count];

        // Create for every organization an entry with an array of articles
        if (in_array($current_article->get_organization(), array_keys($organizations_for_orders))) {
            array_push($organizations_for_orders[$current_article->get_organization()], $current_article);
            array_push($organization_article_amount[$current_article->get_organization()], $amount[$count]);
        } else {
            $organizations_for_orders[$current_article->get_organization()] = array($current_article);
            $organization_article_amount[$current_article->get_organization()] = array($amount[$count]);
        }
        ?>


        <div class="callout callout-danger">
            <div class="row mb-3 align-items-center">
                <div class="col-4 col-md-2">
                    <img src="data:image;base64,<?php echo $current_article->get_first_image(); ?>" width="90"
                         height="90">
                </div>
                <div class="col-6 col-lg-8">
                    <h6><?php echo $current_article->get_title() ?></h6>
                    <p>Shipping Time: <?php echo $current_article->get_days_until_shipping() ?> days</p>
                    <p>Shipping Cost: <?php
                        if ($current_article->get_shipping_cost() == 0) {
                            echo 'Free';
                        } else {
                            echo $current_article->get_shipping_cost();
                        }
                        ?></p>
                </div>
                <div class="col-12 col-md-4 col-lg-2 d-flex flex-column align-items-start">
                    <div class="row">
                        <div class="col-6 col-md-12 mt-2 mt-md-0">
                            Price:
                            <b>
                                <?php
                                $c_price = $current_article->get_current_price();
                                echo number_format($c_price, '2', '.', ''); ?> € </b>
                        </div>
                        <div class="col-6 col-md-12">
                            <div class="d-flex align-items-center">
                                <label for="amount_<?php echo $current_article->get_id() ?>"
                                       class="form-label align-middle">Amount:</label>
                                <input type="number" id="amount_<?php echo $current_article->get_id() ?>"
                                       class="form-control mb-2 ms-2" name="quantity" min="1"
                                       onchange="updateAmountOfArticle(<?php echo $current_article->get_id() ?>)"
                                       max="<?php echo $current_article->get_current_available() ?> "
                                       value="<?php
                                       $c_amount = $amount[$count];
                                       echo $c_amount;
                                       ?>"
                                       onKeyDown="return false">
                                <input type="hidden" id="availableStock_<?php echo $current_article->get_id() ?>"
                                       value="<?php echo $current_article->get_current_available() ?>">
                            </div>
                        </div>
                        <button class="col-12 mt-auto btn btn-danger"
                                onclick="deleteFromShoppingBasket(<?php echo $current_article->get_id() ?>)">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                    </div>

                </div>

            </div>
        </div>
        <?php
        $sum += $c_amount * $c_price;
    }
    $_SESSION['organizations_for_orders'] = $organizations_for_orders;
    $_SESSION['organization_article_amount'] = $organization_article_amount;
    ?>
    <hr/>
    <p style="text-align: right; padding-right: 1.25rem">Total: <b
                id="sumOfBasket"><?php echo number_format($sum, '2', '.', '') ?> €</b></p>

    <div class="d-flex">
        <button class="btn btn-danger mx-2 mb-5" style="width: 50%" data-bs-toggle="modal"
                data-bs-target="#exampleModal">
            Clear Basket
        </button>
        <a class="btn btn-warning mx-2 mb-5 <?php
        if (sizeof($articles) == 0) {
            echo "disabled";
        }
        ?>" style="width: 50%" role="button" onclick="shoppingCartAfterBuy()" href="../php_backend/order_complete.php">
            Buy now
        </a>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Attention!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Do you want to clear your basket?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                            onclick="clearShoppingBasket()">Yes
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.inc.php"); ?>


</body>
</html>