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
    <div class="row my-4">
        <!-- Menu -->
        <?php include $_SERVER["DOCUMENT_ROOT"] . "/includes/seller_dashboard_menu.inc.php"; ?>
        <!-- Spacing -->
        <div class="col-md-1"></div>
        <!-- Content -->
        <div class="col-md-8">
            <h3>Customer orders for your products</h3>
            <?php
            //Initialize Order Controller
            $order_controller = new order_controller();
            //Get Orders for Current User
            $orders = $order_controller->get_orders_for_organization($_SESSION["user"]->organization);
            //loop over orders
            foreach ($orders as $order) {

                echo "<hr>";
                include $_SERVER["DOCUMENT_ROOT"] . "/includes/display_helpers/order_overview_seller.inc.php";

            } ?>


        </div>
    </div>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.inc.php"); ?>

</body>
</html>
