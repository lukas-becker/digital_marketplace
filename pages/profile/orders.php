<?php
include $_SERVER['DOCUMENT_ROOT'] . "/includes/php_head.inc.php";
$user = $_SESSION["user"];

if (!isset($_SESSION["login"])) {
    header("Location: /pages/login.php");
    die();
}

?>
<!doctype html>
<html lang="en">
<head>
    <?php
    $title = "Marketplace";
    include $_SERVER['DOCUMENT_ROOT'] . "/includes/html_head.inc.php";
    ?>

    <script>
        function hoverAddress(id) {
            const element = document.getElementById("address_" + id);
            if (element != null) {
                element.classList.remove("bg-white");
                element.classList.add("bg-success");
            }
        }

        function leaveAddress(id) {
            const element = document.getElementById("address_" + id);
            if (element != null) {
                element.classList.add("bg-white");
                element.classList.remove("bg-success");
            }
        }
    </script>
</head>
<body>
<!--Include Navbar-->
<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/navbar.inc.php"); ?>

<div class="container my-5 pt-5">
    <h1>Accountinformation for <?php echo $user->first_name; ?></h1>
    <div class="row mt-4">
        <!-- Menu -->
        <?php include $_SERVER["DOCUMENT_ROOT"] . "/includes/profile_menu.inc.php"; ?>
        <!-- Spacing -->
        <div class="col-md-1"></div>
        <!-- Content -->
        <div class="col-md-8">
            <h3>Your Orders</h3>
            <?php
            //Initialize Order Controller
            $order_controller = new order_controller();
            //Get Orders for Current User
            $orders = $order_controller->get_orders_for_user($_SESSION["user"]);
            //loop over orders
            foreach ($orders as $order) {
                echo "<hr>";
                include $_SERVER["DOCUMENT_ROOT"] . "/includes/display_helpers/order_overview_customer.inc.php";


            } ?>


        </div>
    </div>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.inc.php"); ?>

</body>
</html>
