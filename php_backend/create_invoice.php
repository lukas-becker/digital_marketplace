<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/php_backend/dompdf/autoload.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controller/order_controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controller/user_controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/model/Order.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/model/User.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/model/Address.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/model/Organization.php';

// reference the Dompdf namespace
use Dompdf\Dompdf;

if (isset($_REQUEST["id"])) {
    create_invoice($_REQUEST["id"]);
    $resultArray = ["status" => true, "link" => "/saved_invoices/invoice_" . $_REQUEST["id"] . ".pdf"];
    echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
}

if (isset($_REQUEST["deleteId"])) {
    delete_invoice($_REQUEST["deleteId"]);
}

function create_invoice($order)
{

    //Send  E-Mail
    include $_SERVER['DOCUMENT_ROOT'] . '/php_backend/invoice_template.php';

    //ID was supplied
    if (is_numeric($order)) {
        $id = $order;
    } else {
        $id = $order->id;
    }

    $order = new Order($id);

    $user_controller = new user_controller();
    $user = $user_controller->get_user_for_order($order);
    $user_address = $user->get_primary_address();

    $invoice_html = str_replace("%r_order_number_%r", $order->id, $invoice_content);

    $order_date = DateTime::createFromFormat('Y-m-d H:i:s', $order->order_date);
    $order_date_string = $order_date->format('d.m.Y');
    $invoice_html = str_replace("%r_order_date_%r", $order_date_string, $invoice_html);

    $customer_address = $user->first_name . " " . $user->last_name . "<br>" . $user_address->street . " " . $user_address->number . "<br>" . $user_address->zip . " " . $user_address->city;
    $invoice_html = str_replace("%r_address_customer_%r", $customer_address, $invoice_html);

    $seller = $order->get_seller();
    $seller_address = $seller->name . "<br>" . $seller->street . " " . $seller->nr . "<br>" . $seller->zip . " " . $seller->city;
    $invoice_html = str_replace("%r_address_seller_%r", $seller_address, $invoice_html);

    $article_string = "";
    $total = 0.0;
    $i = 1;
    $length = count($order->articles);
    foreach ($order->articles as $order_item) {
        if ($i == $length) {
            $article_string .= "<tr class='item last'>";

            $article_string .= "<td>" . $order_item->article->get_title() . "</td>";
            $article_string .= "<td>" . $order_item->amount . "</td>";
            $article_string .= "<td>" . number_format($order_item->price, 2, ",", "") . "€</td>";

            $article_string .= "</tr>";

            $total += $order_item->amount * $order_item->price;

            continue;
        }

        $article_string .= "<tr class='item'>";

        $article_string .= "<td>" . $order_item->article->get_title() . "</td>";
        $article_string .= "<td>" . $order_item->amount . "</td>";
        $article_string .= "<td>" . number_format($order_item->price, 2, ",", "") . "€</td>";

        $article_string .= "</tr>";

        $total += $order_item->amount * $order_item->price;
        $i++;
    }
    $article_string .= "<tr class='total'>";

    $article_string .= "<td>" . "</td>";
    $article_string .= "<td>" . "</td>";
    $article_string .= "<td>" . number_format($total, 2, ",", "") . "€</td>";

    $article_string .= "</tr>";

    $invoice_html = str_replace("%r_ordered_articles_%r", $article_string, $invoice_html);

    // instantiate and use the dompdf class
    $dompdf = new Dompdf();
    $dompdf->loadHtml($invoice_html);

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to local Storage
    $output = $dompdf->output();
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/saved_invoices/' . 'invoice_' . $order->id . '.pdf', $output);
}

function delete_invoice($number)
{
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/saved_invoices/' . 'invoice_' . $number . '.pdf')) {
        unlink($_SERVER['DOCUMENT_ROOT'] . '/saved_invoices/' . 'invoice_' . $number . '.pdf');
    }
}