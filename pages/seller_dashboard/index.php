<?php
include $_SERVER['DOCUMENT_ROOT'] . "/includes/php_head.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/model/Article.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/model/Organization.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/controller/organization_controller.php";
if (!isset($_SESSION["login"])) {
    header("Location: /pages/login.php");
    die();
}

if ($_SESSION["user"]->organization == 0) {
    header("Location: /index.php?message=no_org");
    die();
} else {
    $org = new Organization($_SESSION["user"]->organization);
    $org_controller = new organization_controller();
}

function format_trend($trend_value)
{
    if ($trend_value > 0) {
        return "+" . number_format($trend_value, 0, "", "");
    } else {
        return number_format($trend_value, 0, "", "");
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <?php
    $title = "Seller Dashboard";
    include $_SERVER['DOCUMENT_ROOT'] . "/includes/html_head.inc.php";
    ?>

    <link rel="stylesheet" href="/style/profile_accordion.css">
</head>

<script src="/scripts/countUp.js"></script>
<script src="/scripts/sellerDashboard.inc.js"></script>

<body>
<!--Include Navbar-->
<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/navbar.inc.php"); ?>

<div class="container my-5 pt-5">
    <h1>Seller dashboard for <?php echo $org->name; ?></h1>
    <div class="row mt-4">
        <!-- Menu -->
        <?php include $_SERVER["DOCUMENT_ROOT"] . "/includes/seller_dashboard_menu.inc.php"; ?>

        <!-- spacing -->
        <div class="col-md-1"></div>

        <!-- Content -->
        <div class="col-md-8">
            <div class="row mb-3" id="sellerIndexBody">

                <!-- Accordion -->
                <div class="accordion my-5">
                    <!-- KPIs -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true"
                                    aria-controls="panelsStayOpen-collapseOne">
                                <h5>Your KPIs for the last 30 days:</h5>
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show"
                             aria-labelledby="panelsStayOpen-headingOne">
                            <div class="accordion-body">
                                <div class="row mb-5 mt-2 text-center">
                                    <div class="col-4">
                                        <h5>Number of orders:</h5>
                                        <span class="text-success fs-1 fw-bold"
                                              id="numOrders"><?php echo $org_controller->get_sales_for_current_month($org) ?></span><br>
                                        <span class="fs-6 fw-bold"><?php echo format_trend($org_controller->get_sales_trend($org)) ?>%</span>
                                        <span class="d-none"
                                              id="numOrdersAmount"><?php echo $org_controller->get_sales_for_current_month($org) ?></span>
                                    </div>
                                    <div class="col-4">
                                        <h5>Articles sold:</h5>
                                        <span class="text-success fs-1 fw-bold"
                                              id="soldArticles"><?php echo $org_controller->get_article_sold_count_for_current_month($org) ?></span><br>
                                        <span class="fs-6 fw-bold"><?php echo format_trend($org_controller->get_article_trend($org)) ?>%</span>
                                        <span class="d-none"
                                              id="soldArticlesAmount"><?php echo $org_controller->get_article_sold_count_for_current_month($org) ?></span>
                                    </div>
                                    <div class="col-4"><h5>Revenue generated:</h5>
                                        <span class="text-success fs-1 fw-bold"
                                              id="revenueGenerated"><?php echo number_format(round($org_controller->get_revenue_for_current_month($org), 2), 2, ",", "") ?>€</span><br>
                                        <span class="fs-6 fw-bold"><?php echo format_trend($org_controller->get_revenue_trend($org)) ?>%</span>
                                        <span class="d-none"
                                              id="revenueGeneratedAmount"><?php echo $org_controller->get_revenue_for_current_month($org) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Most popular product -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false"
                                    aria-controls="panelsStayOpen-collapseTwo">
                                <h5>Your most popular product:</h5>
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse show"
                             aria-labelledby="panelsStayOpen-headingTwo">
                            <div class="accordion-body">
                                <div class="row mb-5 mt-2">
                                    <div class="col-2 col-md-3"></div>
                                    <div class="col-8 col-md-6">
                                        <?php
                                        $article = $org_controller->get_most_selling_product($org);
                                        include $_SERVER["DOCUMENT_ROOT"] . "/includes/display_helpers/article_card.inc.php";
                                        ?>
                                    </div>
                                    <div class="col-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- newest order -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false"
                                    aria-controls="panelsStayOpen-collapseThree">
                                <h5>Latest Customer Order:</h5>
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse show"
                             aria-labelledby="panelsStayOpen-headingThree">
                            <div class="accordion-body">
                                <div class="mb-5">
                                    <?php
                                    //Iniitalize Order Controller
                                    $order_controller = new order_controller();
                                    //Get Orders for Current User
                                    $order = $order_controller->get_newest_order_for_organisation($org);

                                    include $_SERVER["DOCUMENT_ROOT"] . "/includes/display_helpers/order_overview_seller.inc.php";
                                    ?>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Old Display Method -->
                <!--                <!-- Key Performance Indicators -- >-->
                <!--                <hr>-->
                <!--                <h3>Your KPIs for the month of --><?php //echo date("F"); ?><!--:</h3>-->
                <!--                <div class="row mb-5 mt-2 text-center">-->
                <!--                    <div class="col-4">-->
                <!--                        <h5>Number of orders:</h5>-->
                <!--                        <span class="text-success fs-5 fw-bold text-decoration-underline">-->
                <?php //echo $org_controller->get_sales_for_current_month($org) ?><!--</span><br>-->
                <!--                        <span class="fs-6 fw-bold">-->
                <?php //echo ($org_controller->get_sales_trend($org) > 0) ? "+".$org_controller->get_sales_trend($org) : $org_controller->get_sales_trend($org)  ?><!--%</span>-->
                <!--                    </div>-->
                <!--                    <div class="col-4">-->
                <!--                        <h5>Articles sold:</h5>-->
                <!--                        <span class="text-success fs-5 fw-bold text-decoration-underline">-->
                <?php //echo $org_controller->get_article_sold_count_for_current_month($org) ?><!--</span><br>-->
                <!--                        <span class="fs-6 fw-bold">-->
                <?php //echo ($org_controller->get_sales_trend($org) > 0) ? "+".$org_controller->get_article_trend($org) : $org_controller->get_sales_trend($org)  ?><!--%</span>-->
                <!--                    </div>-->
                <!--                    <div class="col-4"><h5>Revenue generated:</h5>-->
                <!--                        <span class="text-success fs-5 fw-bold text-decoration-underline">-->
                <?php //echo number_format(round($org_controller->get_revenue_for_current_month($org),2),2,",","") ?><!--€</span><br>-->
                <!--                        <span class="fs-6 fw-bold">-->
                <?php //echo ($org_controller->get_sales_trend($org) > 0) ? "+".$org_controller->get_revenue_trend($org) : $org_controller->get_sales_trend($org)  ?><!--%</span>-->
                <!--                    </div>-->
                <!--                </div>-->
                <!---->
                <!--                <!-- Most visited product -- >-->
                <!--                <hr>-->
                <!--                <h3>Your most popular product:</h3>-->
                <!--                <div class="row mb-5 mt-2">-->
                <!--                    <div class="col-4"></div>-->
                <!--                    <div class="col-4">-->
                <!--                        --><?php //$article = $org_controller->get_most_selling_prodcut($org) ?>
                <!--                        <div class="card mx-0 p-0">-->
                <!--                            <a href="/pages/article/detailed_article.php?article=-->
                <?php //echo $article->get_id() ?><!--"><img-->
                <!--                                        src="data:image;base64,-->
                <?php //echo $article->get_first_image(); ?><!--" class="card-img-top" alt="..."/></a>-->
                <!--                            <a href="/pages/article/detailed_article.php?article=-->
                <?php //echo $article->get_id() ?><!--"><h5-->
                <!--                                        class="card-title px-3 pt-2">-->
                <?php //echo $article->get_title(); ?><!--</h5></a>-->
                <!--                            <div class="card-body pt-0 pb-1">-->
                <!--                                <p class="card-text">-->
                <?php //echo $article->get_description(); ?><!--</p>-->
                <!--                            </div>-->
                <!--                            <div class="card-footer">-->
                <!--                                Price: --><?php //echo number_format($article->get_current_price(), 2, ",", ""); ?>
                <!--                            </div>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                    <div class="col-4"></div>-->
                <!--                </div>-->
                <!---->
                <!--                <!-- newest Order -- >-->
                <!--                <hr>-->
                <!--                <h3>Latest Customer Order:</h3>-->
                <!--                <div class="mb-5">-->
                <!--                    --><?php
                //                    //Iniitalize Order Controller
                //                    $order_controller = new order_controller();
                //                    //Get Orders for Current User
                //                    $order = $order_controller->get_newest_order_for_organisation($org);
                //                    ?>
                <!---->
                <!--                    <!-- Display Callout with border appropriate to order state -- >-->
                <!--                    <div class="callout -->
                <?php //echo $order->state == "Completed" ? "callout-success" : "callout-warning"; ?><!--">-->
                <!--                        <h5 class="mb-1">Order number -->
                <?php //echo $order->id . " (" . $order->state . ")"?><!--</h5>-->
                <!--                        <p class="mb-2">Order date: --><?php //echo $order->order_date?><!--</p>-->
                <!--                        --><?php
                //                        //variables to store sum of articles and total amount
                //                        $sum = 0;
                //                        $count = 0;
                //                        //loop over articles in order
                //                        foreach ($order->articles as $order_item){ ?>
                <!--                            <!-- One Article: -- >-->
                <!--                            <div class="row mb-3">-->
                <!--                                <div class="col-2">-->
                <!--                                    <img src="data:image;base64,-->
                <?php //echo $order_item->article->get_first_image(); ?><!--" width="90" height="90">-->
                <!--                                </div>-->
                <!--                                <div class="col-4">-->
                <!--                                    <h6>-->
                <?php //echo $order_item->article->get_title() ?><!--</h6>-->
                <!--                                    <p>-->
                <?php //echo substr($order_item->article->get_description(), 0, 15 ) ?><!--...</p>-->
                <!--                                </div>-->
                <!--                                <div class="col-2">-->
                <!---->
                <!--                                </div>-->
                <!--                                <div class="col-2 d-flex align-items-center">-->
                <!--                                    --><?php
                //                                    $count += $order_item->amount;
                //                                    echo $order_item->amount ?><!-- pcs.-->
                <!--                                </div>-->
                <!--                                <div class="col-2 d-flex align-items-center">-->
                <!--                                    --><?php
                //                                    $sum += $order_item->amount * $order_item->price;
                //                                    echo number_format(($order_item->amount * $order_item->price), 2, ",", "") ?><!-- €-->
                <!--                                </div>-->
                <!--                            </div>-->
                <!--                        --><?php //} ?>
                <!--                        <hr>-->
                <!--                        <!-- Total: -- >-->
                <!--                        <div class="row mb-3">-->
                <!--                            <div class="col-6">-->
                <!---->
                <!--                            </div>-->
                <!--                            <div class="col-2 d-flex align-items-center">-->
                <!--                                <h6>Total:</h6>-->
                <!--                            </div>-->
                <!--                            <div class="col-2 d-flex align-items-center">-->
                <!--                                <h6>--><?php //echo $count ?><!-- pcs.</h6>-->
                <!--                            </div>-->
                <!--                            <div class="col-2 d-flex align-items-center">-->
                <!--                                <h6>-->
                <?php //echo number_format($sum * (1 - $order->applied_rebate),2,",","") ?><!-- €</h6>-->
                <!--                            </div>-->
                <!--                        </div>-->
                <!--                        <!-- Invoice: -- >-->
                <!--                        <div class="row">-->
                <!--                            <div class="col-6">-->
                <!---->
                <!--                            </div>-->
                <!--                            <div class="col-4">-->
                <!--                                <h6>Go to overview</h6>-->
                <!--                            </div>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                </div>-->

            </div>
        </div>
    </div>
</div>


<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.inc.php"); ?>

</body>
</html>
