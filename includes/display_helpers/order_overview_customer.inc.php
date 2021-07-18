<!-- Template for displaying an order in the customer view -->
<!-- Therefore a variable $order of the class Order has to be initialized -->

<?php global $order ?>

<!-- Display Callout with border appropriate to order state -->
<div class="callout <?php echo $order->state == "Completed" ? "callout-success" : "callout-warning"; ?> mb-5">
    <h5 class="mb-1">Order number <?php echo $order->id . " (" . $order->state . ")" ?></h5>
    <p class="mb-2">Order date: <?php echo $order->order_date ?></p>
    <?php
    //variables to store sum of articles and total amount
    $sum = 0;
    $count = 0;
    //loop over articles in order
    foreach ($order->articles as $order_item) { ?>
        <!-- One Article: -->
        <div class="row mb-3">
            <div class="col-6 col-md-2">
                <img src="data:image;base64,<?php echo $order_item->article->get_first_image(); ?>" width="90"
                     height="90" alt="Image of Article">
            </div>
            <div class="col-6 col-md-4">
                <h6>
                    <a href="/pages/article/detailed_article.php?article=<?php echo $order_item->article->get_id(); ?>"><?php echo $order_item->article->get_title() ?></a>
                </h6>

                <p><?php echo substr(trim(strip_tags($order_item->article->get_description())), 0, 30) ?>...</p>
            </div>
            <div class="d-sm-none d-md-block col-md-2">

            </div>
            <div class="col-6 col-md-2 d-flex align-items-center">
                <?php
                $count += $order_item->amount;
                echo $order_item->amount ?> pcs.
            </div>
            <div class="col-6 col-md-2 d-flex align-items-center">
                <?php
                $sum += $order_item->amount * $order_item->price;
                echo number_format($order_item->amount * $order_item->price, 2, ",", ""); ?> €
            </div>
        </div>
    <?php } ?>
    <hr>
    <!-- Total: -->
    <div class="row mb-3">
        <div class="d-sm-none d-md-block col-md-6">

        </div>
        <div class="col-4 col-md-2 d-flex align-items-center">
            <h6>Total:</h6>
        </div>
        <div class="col-4 col-md-2 d-flex align-items-center">
            <h6><?php echo $count ?> pcs.</h6>
        </div>
        <div class="col-4 col-md-2 d-flex align-items-center">
            <h6><?php echo number_format($sum * (1 - $order->applied_rebate), 2, ",", ""); ?> €</h6>
        </div>
    </div>
    <!-- Invoice: -->
    <div class="row">
        <div class="d-sm-none col-md-6">

        </div>
        <div class="d-flex align-items-center justify-content-between">
            <div class="col-6">
                <a id="invoiceNr<?php echo $order->id; ?>" class="fs-6"
                   onclick="downloadInvoice(<?php echo $order->id; ?>)"
                   href="#invoiceNr<?php echo $order->id; ?>">Download invoice</a>
            </div>

            <?php if ($order->paid == 0) { ?>
                <button class="btn btn-primary col-6" onclick="payOrder(<?php echo $order->id ?>)">
                    Pay
                </button>
            <?php } else { ?>
                <div class="col-6 alert alert-success text-center mb-0">
                    Paid
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>
    /**
     * Create an invoice as pdf for the order
     *
     * @param orderID The order id for which an invoice is created
     */
    function downloadInvoice(orderID) {
        const xmlhttp = new XMLHttpRequest();

        xmlhttp.open("POST", "/php_backend/create_invoice.php?id=" + orderID, true);

        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                const result = JSON.parse(this.responseText);
                if (result["status"] == true) {
                    link = document.createElement("a"); //create 'a' element
                    link.setAttribute("href", result["link"]); //replace "file" with link to file you want to download
                    link.setAttribute("download", "invoice_" + orderID + ".pdf");// replace "file" here too
                    link.click(); //virtually click <a> element to initiate download
                    let xmlhttp2 = new XMLHttpRequest();
                    xmlhttp2.open("POST", "/php_backend/create_invoice.php?deleteId=" + orderID, true);
                    xmlhttp2.send();
                } else if (result["status"] == false) {

                } else {

                }
            }
        };
        xmlhttp.send();
    }

    function payOrder(order) {
        const xmlhttp = new XMLHttpRequest();

        let parameter = "pay_order=true";
        parameter += "&order=" + order;

        xmlhttp.open("POST", "/controller/order_controller.php", true);
        xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                const result = JSON.parse(this.responseText);
                if (result["status"] == true) {
                    location.reload();
                } else if (result["status"] == false) {

                } else {

                }

            }
        };
        xmlhttp.send(parameter);
    }
</script>