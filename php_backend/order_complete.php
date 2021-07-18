<?php
include $_SERVER['DOCUMENT_ROOT'] . "/includes/php_head.inc.php";
$order_controller = new order_controller();
$organizations_for_orders = $_SESSION['organizations_for_orders'];
$organization_article_amount = $_SESSION['organization_article_amount'];
$orders = array();

$article_controller = new article_controller();
$order_ids = array();
foreach (array_keys($organizations_for_orders) as $org_id) {
    $current_order = $order_controller->create_order();
    array_push($order_ids, $current_order);
    $article_of_org = $organizations_for_orders[$org_id];
    $amount_of_article = $organization_article_amount[$org_id];
    for ($count = 0; $count < sizeof($article_of_org); $count++) {
        $article = $article_of_org[$count];
        $article_controller->decrease_available_articles($article, $amount_of_article[$count]);
        $order_controller->add_article_to_order($current_order, $article, $amount_of_article[$count]);
    }
    $order_object = new Order($current_order);
    array_push($orders, $order_object);
}
$_SESSION['order'] = $order_ids;
print_r($_SESSION['order']);

header("Location: /pages/order_complete.php");
?>