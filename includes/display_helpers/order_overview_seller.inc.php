<!-- Template for displaying an order in the seller view -->
<!-- Therefore a variable $order of the class Order has to be initialized -->

<?php global $order ?>

<!-- Display Callout with border appropriate to order state -->
<div class="callout <?php echo $order->state == "Completed" ? "callout-success" : "callout-warning"; ?>">
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
            <div class="d-sm-none col-md-2">

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
        <div class="d-sm-none d-md-block col-md-6">

        </div>
        <div class="col-12 col-md-4">
            <h6><a href="/pages/seller_dashboard/order_overview.php?id=<?php echo $order->id ?>">Go to overview</a></h6>
        </div>
    </div>
</div>