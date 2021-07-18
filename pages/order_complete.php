<?php
include $_SERVER['DOCUMENT_ROOT'] . "/includes/php_head.inc.php";
$orders = array();
foreach ($_SESSION['order'] as $order_id) {
    $co = new Order($order_id);
    array_push($orders, $co);
}
?>
<!doctype html>
<html lang="en">
<head>
    <?php
    $title = "Order complete";
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/html_head.inc.php");
    ?>
</head>
<body>
<!--Include Navbar-->
<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/navbar.inc.php"); ?>

<div class="container my-5 pt-5">
    <h1>Thank you for your order!</h1>
    <h2>Your orders:</h2>
    <?php foreach ($orders as $order) {
        include $_SERVER['DOCUMENT_ROOT'] . "/includes/display_helpers/order_overview_customer.inc.php";
    } ?>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.inc.php"); ?>


</body>
</html>
