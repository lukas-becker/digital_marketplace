<?php
include $_SERVER['DOCUMENT_ROOT'] . "/includes/php_head.inc.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/Organization.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/User.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/Address.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/Order.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/controller/user_controller.php";
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

if (!isset($_GET["id"])) {
    header("Location: /pages/seller_dashboard/index.php");
    die();
}
$order = new Order($_GET["id"]);
$user_controller = new user_controller();
$customer = $user_controller->get_user_for_order($order);
try {
    $address = $customer->get_primary_address();
} catch (Exception $e) {
    throw new Exception($e->getMessage());
}

?>
<!doctype html>
<html lang="en">
<head>
    <?php
    $title = "Seller Dashboard - Orders";
    include $_SERVER['DOCUMENT_ROOT'] . "/includes/html_head.inc.php";
    ?>

    <script>
        function markAsShipped(id) {
            const xmlhttp = new XMLHttpRequest();

            xmlhttp.open("GET", "/controller/order_controller.php?mark_as_shipped=" + id, true);
            //xmlhttp.setRequestHeader("Content-type", "multipart/form-data");


            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    location.reload();
                }
            };

            xmlhttp.send();
        }
    </script>
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
            <h3>Overview for Order Nr. <?php echo $order->id; ?></h3>
            <hr>

            <!-- Order status -->
            <h4>Change Status:</h4>
            <!-- Callout Desktop -->
            <div class="d-none d-md-block callout px-5 mb-5">
                <div class="position-relative mx-5 my-4">
                    <div class="progress" style="height: 3px;">
                        <?php if ($order->state == "Placed" && !$order->paid) { ?>
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                                 role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                                 aria-valuemax="100"></div>
                        <?php } ?>
                        <?php if ($order->state == "Placed" && $order->paid) { ?>
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                                 role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0"
                                 aria-valuemax="100"></div>
                        <?php } ?>
                        <?php if ($order->state == "Shipped") { ?>
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                                 role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0"
                                 aria-valuemax="100"></div>
                        <?php } ?>
                        <?php if ($order->state == "Completed") { ?>
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                                 role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0"
                                 aria-valuemax="100"></div>
                        <?php } ?>
                    </div>
                    <button type="button"
                            class="position-absolute top-0 start-10 translate-middle btn btn-<?php echo($order->paid ? "success" : "secondary") ?> rounded-pill">
                        Paid
                    </button>
                    <?php if ($order->state == "Placed") { ?>
                        <button type="button"
                                class="position-absolute top-0 start-50 translate-middle btn btn-primary rounded-pill"
                                onclick="markAsShipped(<?php echo $order->id; ?>)" <?php echo($order->paid ? "" : "disabled") ?>>
                            Shipped
                        </button>
                    <?php } else if ($order->state == "Shipped" || $order->state == "Completed") { ?>
                        <button type="button"
                                class="position-absolute top-0 start-50 translate-middle btn btn-success rounded-pill">
                            Shipped
                        </button>
                    <?php } ?>
                    <?php if ($order->state == "Placed") { ?>
                        <button type="button"
                                class="position-absolute top-0 start-100 translate-middle btn btn-secondary rounded-pill">
                            Completed
                        </button>
                    <?php } else if ($order->state == "Shipped") { ?>
                        <button type="button"
                                class="position-absolute top-0 start-100 translate-middle btn btn-primary rounded-pill">
                            Completed
                        </button>
                    <?php } else { ?>
                        <button type="button"
                                class="position-absolute top-0 start-100 translate-middle btn btn-success rounded-pill">
                            Completed
                        </button>
                    <?php } ?>

                </div>
            </div>
            <!-- Callout Mobile -->
            <div class="d-md-none callout px-5 mb-5">
                <div class="row">
                    <button type="button" class="col-12 mb-3 btn btn-success">
                        Order Placed
                    </button>
                    <?php if ($order->state == "Placed") { ?>
                        <button type="button" class="col-12 mb-3 btn btn-primary"
                                onclick="markAsShipped(<?php echo $order->id; ?>)">
                            Shipped
                        </button>
                    <?php } else if ($order->state == "Shipped" || $order->state == "Completed") { ?>
                        <button type="button" class="col-12 mb-3 btn btn-success">
                            Shipped
                        </button>
                    <?php } ?>
                    <?php if ($order->state == "Placed") { ?>
                        <button type="button" class="col-12 mb-3 btn btn-secondary">
                            Completed
                        </button>
                    <?php } else if ($order->state == "Shipped") { ?>
                        <button type="button" class="col-12 mb-3 btn btn-primary">
                            Completed
                        </button>
                    <?php } else { ?>
                        <button type="button" class="col-12 mb-3 btn btn-success">
                            Completed
                        </button>
                    <?php } ?>

                </div>

            </div>

            <h4>Customer Information:</h4>
            <div class="callout fw-bold mb-5">
                Name: <?php echo $customer->first_name . " " . $customer->last_name; ?><br>
                Street: <?php echo $address->street . " " . $address->number; ?><br>
                City: <?php echo $address->zip . " " . $address->city; ?><br>
            </div>

            <h4>Ordered Articles</h4>
            <div class="callout mb-5">
                <?php
                foreach ($order->articles as $order_item) { ?>
                    <!-- One Article: -->
                    <div class="row my-2">
                        <div class="col-4 col-md-2">
                            <img src="data:image;base64,<?php echo $order_item->article->get_first_image(); ?>"
                                 width="90" height="90">
                        </div>
                        <div class="col-5 col-md-4 d-flex align-items-center">
                            <a href="/pages/article/detailed_article.php?article=<?php echo $order_item->article->get_id(); ?>">
                                <h6><?php echo $order_item->article->get_title() ?></h6></a>
                        </div>
                        <div class="col-3 col-md-2 d-flex align-items-center fw-bold">
                            <?php
                            echo $order_item->amount ?> pcs.
                        </div>
                    </div>
                <?php }
                ?>
            </div>

        </div>
    </div>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.inc.php"); ?>

</body>
</html>
